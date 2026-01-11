# Code Review: User Module

**Дата:** 2026-01-11
**Ревьювер:** Symfony Migration Mentor
**Модуль:** `src/Model/User/`

---

## ⛔ КРИТИЧЕСКИЕ БАГИ (Приоритет: НЕМЕДЛЕННО)

### 1. Бесконечная рекурсия в `getRoles()`

**Файл:** `src/Model/User/Entity/User/User.php:141-144`

```php
public function getRoles(): array
{
    return $this->getRoles(); // ← БЕСКОНЕЧНАЯ РЕКУРСИЯ!
}
```

**Проблема:** Метод вызывает сам себя. Это приведёт к `StackOverflow` при первом обращении.

**Решение:**
```php
public function getRoles(): array
{
    return ['ROLE_USER'];
}
```

---

### 2. `signUpByEmail` вызывается как static, но он instance метод

**Файл:** `src/Model/User/UseCase/SignUp/Request/Handler.php:40-46`

```php
$user = User::signUpByEmail(
    Id::next(),
    new DateTimeImmutable(),
    Email::fromString($command->email),
    $this->hasher->hash($command->password),
    $token,
);
```

**Но в User.php:40:**
```php
public function signUpByEmail(
    Email  $email,
    string $hash,
    string $token
): void  // ← instance method, returns void!
```

**Проблема:** Несовместимость сигнатур. Код не будет работать.

**Решение:** Переделать на named constructor:
```php
// User.php
public static function signUpByEmail(
    Id $id,
    DateTimeInterface $date,
    Email $email,
    string $hash,
    string $token
): self {
    $user = new self($id, $date);
    $user->email = $email;
    $user->password = $hash;
    $user->confirmToken = $token;
    $user->status = UserStatus::WAIT;
    return $user;
}
```

---

### 3. Неправильный namespace в Confirm

**Файлы:**
- `src/Model/User/UseCase/SignUp/Confirm/Handler.php:5`
- `src/Model/User/UseCase/SignUp/Confirm/Command.php:5`

```php
namespace App\Model\User\UseCase\SignUp\Request; // ← ДОЛЖНО БЫТЬ Confirm!
```

**Проблема:** Два Handler'а и два Command'а с одинаковым namespace. Autoloader загрузит только один.

**Решение:** Исправить namespace на `App\Model\User\UseCase\SignUp\Confirm`

---

### 4. Токен генерируется дважды

**Файл:** `src/Model/User/UseCase/SignUp/Request/Handler.php:38,47`

```php
$token = $this->tokenGenerator->generate(); // строка 38

$user = User::signUpByEmail(
    // ...
    $token, // используется первый токен
);
$token = $this->tokenGenerator->generate(); // строка 47 - ПЕРЕЗАТИРАЕТСЯ!
$this->sender->send($user->getEmail(), $token); // отправляется ДРУГОЙ токен!
```

**Проблема:** Пользователь получит токен, которого нет в базе. Confirmation не сработает.

**Решение:** Удалить строку 47.

---

### 5. `readonly class` с присвоением в конструкторе

**Файл:** `src/Model/User/Entity/User/Network.php:10,15,23`

```php
final readonly class Network
{
    private Id $id; // ← Не в promoted properties

    private function __construct(...)
    {
        $this->id = Id::next(); // ← Cannot modify readonly property
    }
}
```

**Проблема:** `readonly class` делает все свойства readonly. Присвоение после объявления невозможно.

**Решение:**
```php
final readonly class Network
{
    private function __construct(
        private Id $id,
        private User $user,
        public string $network,
        public string $identity
    ) {
        // валидация
    }

    public static function fromNetwork(User $user, string $network, string $identity): self
    {
        return new self(
            id: Id::next(),
            user: $user,
            network: $network,
            identity: $identity
        );
    }
}
```

---

## 🔴 БАГИ В РЕПОЗИТОРИИ (Приоритет: СРОЧНО)

### 6. Неверное поле в `findByConfirmToken`

**Файл:** `src/Model/User/Repository/UserRepository.php:71`

```php
return $this->findOneBy(['token' => $token]); // ← поле называется 'confirmToken'!
```

**Решение:**
```php
return $this->findOneBy(['confirmToken' => $token]);
```

---

### 7. Неверное имя связи в `hasByNetworkIdentity`

**Файл:** `src/Model/User/Repository/UserRepository.php:78`

```php
->innerJoin('user.network', 'network') // ← связь называется 'networks'
```

**Решение:**
```php
->innerJoin('user.networks', 'network')
```

---

### 8. `getResetToken()` может вернуть null

**Файл:** `src/Model/User/Entity/User/User.php:84-87`

```php
public function getResetToken(): ResetToken // ← отсутствует ?
{
    return $this->resetToken; // может быть null!
}
```

**Решение:**
```php
public function getResetToken(): ?ResetToken
{
    return $this->resetToken;
}
```

---

### 9. Вызов несуществующего метода интерфейса

**Файл:** `src/Model/User/UseCase/SignUp/Request/Handler.php:35`

```php
if ($this->userRepository->findOneBy(['email' => $mail])) {
```

**Проблема:** Метод `findOneBy` отсутствует в `UserRepositoryInterface`.

**Решение:** Добавить метод в интерфейс или использовать `getByEmail`:
```php
if ($this->userRepository->getByEmail($mail)) {
```

---

## 🟠 АРХИТЕКТУРНЫЕ ПРОБЛЕМЫ (Приоритет: ВАЖНО)

### 10. Отсутствует `declare(strict_types=1)`

**Файл:** `src/Model/User/Entity/User/User.php`

Единственный файл без strict types.

---

### 11. Неиспользуемый import `AllowDynamicProperties`

**Файл:** `src/Model/User/Entity/User/User.php:6`

```php
use AllowDynamicProperties; // ← нигде не используется, deprecated в PHP 8.2
```

---

### 12. Публичные сеттеры нарушают инкапсуляцию

**Файл:** `src/Model/User/Entity/User/User.php:131-134, 181-184`

```php
public function setPassword(string $password): void
public function setConfirmToken(?string $token): void
```

**Проблема:** Анемичная модель. Любой может изменить состояние без бизнес-правил.

**Решение:** Сделать private или заменить на методы с бизнес-логикой:
```php
public function resetPassword(string $newPasswordHash, ResetToken $token, DateTimeImmutable $now): void
{
    if ($this->resetToken === null || !$this->resetToken->isExpiredTo($now)) {
        throw new DomainException('Invalid or expired reset token.');
    }
    if ($this->resetToken->getToken() !== $token->getToken()) {
        throw new DomainException('Token mismatch.');
    }
    $this->password = $newPasswordHash;
    $this->resetToken = null;
}
```

---

### 13. Wrong Exception в Email Value Object

**Файл:** `src/Model/User/ValueObject/Email.php:7`

```php
use PharIo\Manifest\InvalidEmailException; // ← из библиотеки для PHAR!
```

**Решение:** Создать свой exception:
```php
// src/Model/User/Exception/InvalidEmailException.php
namespace App\Model\User\Exception;

class InvalidEmailException extends \InvalidArgumentException {}
```

---

### 14. Value Objects без метода `equals()`

**Файлы:**
- `src/Model/User/ValueObject/Email.php`
- `src/Model/User/ValueObject/Id.php`

**Проблема:** Невозможно корректно сравнить два Value Object.

**Решение:**
```php
// Email.php
public function equals(self $other): bool
{
    return $this->email === $other->email;
}

// Id.php
public function equals(self $other): bool
{
    return $this->id === $other->id;
}

public static function fromString(string $id): self
{
    return new self($id);
}

public function __toString(): string
{
    return $this->id;
}
```

---

### 15. `Email` Value Object без типа свойства

**Файл:** `src/Model/User/ValueObject/Email.php:11`

```php
private $email; // ← нет типа
```

**Решение:**
```php
private string $email;
```

---

### 16. Непоследовательное использование `readonly`

**Файлы:**
- `src/Model/User/UseCase/SignUp/Request/Handler.php` — `readonly class`
- `src/Model/User/UseCase/Reset/Request/Handler.php` — обычный class
- `src/Model/User/UseCase/Network/Auth/Handler.php` — обычный class

**Решение:** Сделать все Handler'ы `readonly`.

---

### 17. Command без immutability

**Файлы:** Все Command классы

```php
class Command
{
    public string $email;
    public string $password;
}
```

**Решение:**
```php
final readonly class Command
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}
}
```

---

### 18. `ResetTokenSender::send()` без аргументов

**Файл:** `src/Model/User/UseCase/Reset/Request/Handler.php:40`

```php
$this->resetTokenSender->send(); // ← как отправить без email и токена?
```

**Решение:** Передать необходимые данные:
```php
$this->resetTokenSender->send($user->getEmail(), $user->getResetToken());
```

И обновить интерфейс `ResetTokenSenderInterface`.

---

### 19. Неправильное использование `#[\Deprecated]`

**Файл:** `src/Model/User/Entity/User/User.php:165`

```php
#[\Deprecated]
public function eraseCredentials(): void
```

**Проблема:** Атрибут `#[\Deprecated]` для пометки своего кода. Это метод интерфейса Symfony.

**Решение:** Убрать атрибут, оставить пустую реализацию.

---

### 20. Закомментированный код

**Файл:** `src/Model/User/Repository/UserRepository.php:40-63`

**Решение:** Удалить. Git помнит историю.

---

## 🟡 PRIMITIVE OBSESSION

| Место | Примитив | Рекомендуемый Value Object |
|-------|----------|---------------------------|
| `Network.php` | `string $network` | `NetworkType` (enum) |
| `Network.php` | `string $identity` | `NetworkIdentity` |
| `User.php` | `string $confirmToken` | `ConfirmToken` |
| `User.php` | `string $password` | `PasswordHash` |

---

## 🟡 DDD НАРУШЕНИЯ

### Flusher в Domain слое

**Проблема:** `FlasherInterface` — инфраструктурная концепция. Domain не должен знать о транзакциях.

**Решение:** Использовать Unit of Work паттерн или вызывать flush в Application слое (контроллере/команде).

---

### Отсутствуют Domain Events

Для таких действий как регистрация, подтверждение, сброс пароля должны публиковаться события:
- `UserRegistered`
- `UserConfirmed`
- `PasswordResetRequested`

---

## 🧪 ПРОБЛЕМЫ В ТЕСТАХ

### 1. `expectException` после вызова метода

**Файл:** `tests/Unit/Model/User/Entity/User/SignUp/ConfirmTest.php:28-34`

```php
public function testAlready():void
{
    $user = $this->buildSignUpUser();
    $user->confirmSignUp();
    $this->expectExceptionMessage('User already confirmed.');
    $user->confirmSignUp(); // ← expectException ДОЛЖЕН БЫТЬ ДО вызова!
}
```

**Решение:**
```php
public function testAlready(): void
{
    $user = $this->buildSignUpUser();
    $user->confirmSignUp();

    $this->expectException(DomainException::class);
    $this->expectExceptionMessage('User already confirmed.');

    $user->confirmSignUp();
}
```

---

### 2. Отсутствующие тесты

- [ ] Тесты для Handler'ов (Unit с моками)
- [ ] Тесты для Repository (Integration)
- [ ] Тесты для Value Objects (Email, Id)
- [ ] Тесты на граничные случаи
- [ ] Тесты на невалидный email
- [ ] Тесты на дублирование email
- [ ] Тесты для Network Auth
- [ ] Тесты для Reset Password

---

## 📋 ЧЕКЛИСТ ИСПРАВЛЕНИЙ

### Критические (блокеры)
- [ ] Исправить `getRoles()` — бесконечная рекурсия
- [ ] Переделать `signUpByEmail` на static factory
- [ ] Исправить namespace в `SignUp/Confirm`
- [ ] Удалить дублирование генерации токена
- [ ] Исправить `readonly class Network`

### Срочные
- [ ] Исправить `findByConfirmToken` — поле `confirmToken`
- [ ] Исправить `hasByNetworkIdentity` — связь `networks`
- [ ] Добавить `?` к return type `getResetToken()`
- [ ] Добавить `getByEmail` или `hasByEmail` в интерфейс

### Важные
- [ ] Добавить `declare(strict_types=1)` в User.php
- [ ] Удалить `AllowDynamicProperties`
- [ ] Убрать публичные сеттеры
- [ ] Создать свой `InvalidEmailException`
- [ ] Добавить `equals()` в Value Objects
- [ ] Сделать все Command `readonly`
- [ ] Удалить закомментированный код

### Тесты
- [ ] Исправить `testAlready` — порядок expectException
- [ ] Добавить тесты для Handler'ов
- [ ] Добавить тесты для Value Objects
- [ ] Добавить integration тесты для Repository

---

## 📚 ССЫЛКИ

- [Symfony 7 Security](https://symfony.com/doc/current/security.html)
- [Doctrine ORM Attributes](https://www.doctrine-project.org/projects/doctrine-orm/en/current/reference/attributes-reference.html)
- [Clean Architecture](https://blog.cleancoder.com/uncle-bob/2012/08/13/the-clean-architecture.html)
- [DDD Building Blocks](https://martinfowler.com/bliki/DDD_Aggregate.html)