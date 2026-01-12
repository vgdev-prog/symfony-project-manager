# LinkedIn Post: CQRS та Domain Events у Symfony

---

🚀 **Рефакторинг до CQRS та впровадження Domain Events**

Сьогодні провів масштабний рефакторинг свого pet-проекту на Symfony 7.4 з DDD архітектурою. Ділюсь досвідом!

---

## 📁 Що змінилось в Application Layer

Перейшов від вкладеної структури UseCase до плоскої Command/Input:

```
❌ До:
UseCase/SignUp/Request/Command.php
UseCase/SignUp/Request/Handler.php

✅ Після:
Command/Input/SignUpByEmailCommand.php
Command/SignUpByEmailHandler.php
```

**Навіщо?** Простіша навігація, готовність до Query (CQRS-lite), чистіший код.

---

## 🎯 Domain Events

Імплементував патерн AggregateRoot з записом подій:

```php
$user = User::signUpByEmail(...);
// Автоматично записується UserSignedUpByEmail event

$this->eventDispatcher->dispatch($user);
// Події диспатчаться після збереження
```

**Важливий інсайт:** Створив два окремі івенти замість одного generic:
- `UserSignedUpByEmail`
- `UserSignedUpByNetwork`

Різні способи реєстрації = різні дані = різні обробники.

---

## 🐛 З чим зіткнувся

**1. Null-safe operator**
```php
// 💥 Fatal error якщо $email = null
$email->getValue()

// ✅ Правильно
$email?->getValue()
```

**2. COUNT замість SELECT**
```php
// 💥 NoResultException коли немає записів
->select('user.id')->getSingleScalarResult()

// ✅ COUNT завжди повертає значення
->select('COUNT(user.id)')->getSingleScalarResult()
```

**3. Забув оновити namespace в services.yaml**

Переніс інтерфейс в інший namespace, а alias залишився старий. Container не компілювався 40 хвилин дебагу 😅

---

## 📝 Що далі

Почав писати unit-тести. Перший клас — `IdTest` для Value Object.

**Помилка новачка:** `expectException()` треба викликати ДО коду, який кидає exception, а не після!

---

## 💡 Головний takeaway

DDD — це не про складність заради складності. Це про:
- Чіткі межі між шарами
- Бізнес-логіка в Domain, не в Controller
- Події замість прямих залежностей
- Код, який легко тестувати

---

Який ваш досвід з DDD у PHP? Використовуєте Domain Events?

#Symfony #PHP #DDD #CQRS #CleanArchitecture #SoftwareDevelopment #Backend #Ukraine #UkrainianDeveloper

---

*Vladyslav Honchar*
*Backend Developer*