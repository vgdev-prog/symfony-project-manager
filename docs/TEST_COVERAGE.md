# Test Coverage Plan

This document describes the test structure for maximum code coverage.

## Test Structure

```
src/
├── Shared/
│   └── Infrastructure/
│       └── Tests/
│           └── Unit/
│               ├── ValueObject/
│               │   ├── IdTest.php
│               │   └── EmailTest.php
│               ├── Exception/
│               │   ├── InvalidEmailExceptionTest.php
│               │   └── InvalidUuidExceptionTest.php
│               └── AggregateRootTest.php
│
└── User/
    └── Infrastructure/
        └── Tests/
            ├── Unit/
            │   ├── Entity/
            │   │   ├── UserSignUpByEmailTest.php
            │   │   ├── UserSignUpByNetworkTest.php
            │   │   ├── UserConfirmTest.php
            │   │   ├── UserPasswordResetTest.php
            │   │   └── NetworkTest.php
            │   ├── ValueObject/
            │   │   └── ResetTokenTest.php
            │   ├── Event/
            │   │   ├── UserSignedUpByEmailTest.php
            │   │   └── UserSignedUpByNetworkTest.php
            │   ├── Command/
            │   │   ├── SignUpByEmailHandlerTest.php
            │   │   ├── SignUpByNetworkHandlerTest.php
            │   │   ├── ConfirmSignUpHandlerTest.php
            │   │   └── RequestPasswordResetHandlerTest.php
            │   └── Services/
            │       └── ResetTokenizerTest.php
            ├── Integration/
            │   └── Repository/
            │       └── UserRepositoryTest.php
            └── Functional/
                └── Http/
                    ├── SignUpControllerTest.php
                    └── ConfirmControllerTest.php
```

## Shared Module Tests

### Unit/ValueObject/IdTest.php

| Test Case | Description |
|-----------|-------------|
| `testNext` | Creates new UUID via `Id::next()` |
| `testFromString` | Creates Id from valid UUID string |
| `testFromStringInvalid` | Throws `InvalidUuidException` for invalid UUID |
| `testEquals` | Two Ids with same value are equal |
| `testNotEquals` | Two different Ids are not equal |
| `testToString` | Returns UUID string representation |

### Unit/ValueObject/EmailTest.php

| Test Case | Description |
|-----------|-------------|
| `testFromString` | Creates Email from valid string |
| `testFromStringInvalid` | Throws `InvalidEmailException` for invalid email |
| `testLowercase` | Email is converted to lowercase |
| `testEquals` | Two Emails with same value are equal |
| `testToString` | Returns email string representation |

### Unit/Exception/InvalidEmailExceptionTest.php

| Test Case | Description |
|-----------|-------------|
| `testMessage` | Exception message contains invalid email |
| `testErrorCode` | Returns `INVALID_EMAIL_FORMAT` |
| `testPublicContext` | Returns array with `email` key |
| `testExamplePublicContext` | Returns example context for OpenAPI |

### Unit/Exception/InvalidUuidExceptionTest.php

| Test Case | Description |
|-----------|-------------|
| `testMessage` | Exception message contains invalid UUID |
| `testErrorCode` | Returns `INVALID_UUID_FORMAT` |
| `testPublicContext` | Returns array with `uuid` key |
| `testExamplePublicContext` | Returns example context for OpenAPI |

### Unit/AggregateRootTest.php

| Test Case | Description |
|-----------|-------------|
| `testRecordEvent` | Event is recorded in aggregate |
| `testReleaseEvents` | Returns and clears recorded events |
| `testHasEvents` | Returns true when events exist |
| `testClearEvents` | Clears all events without returning |
| `testGetEvents` | Returns events without clearing |

## User Module Tests

### Unit/Entity/UserSignUpByEmailTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | User created with correct data |
| `testStatusIsNew` | User status is NEW after signup |
| `testRecordsEvent` | `UserSignedUpByEmail` event is recorded |
| `testEventContainsCorrectData` | Event has userId and email |

### Unit/Entity/UserSignUpByNetworkTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | User created with network identity |
| `testWithEmail` | User created with optional email |
| `testWithoutEmail` | User created without email (null) |
| `testStatusIsActive` | User status is ACTIVE after network signup |
| `testNetworkAttached` | Network is attached to user |
| `testRecordsEvent` | `UserSignedUpByNetwork` event is recorded |

### Unit/Entity/UserConfirmTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | User status changes to ACTIVE |
| `testTokenCleared` | Confirm token is set to null |
| `testAlreadyConfirmed` | Throws exception if already active |

### Unit/Entity/UserPasswordResetTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | Reset token is set |
| `testNotActive` | Throws exception if user not active |
| `testAlreadyRequested` | Throws exception if token not expired |
| `testExpiredCanRequest` | Can request if previous token expired |

### Unit/Entity/NetworkTest.php

| Test Case | Description |
|-----------|-------------|
| `testFromNetwork` | Network created with correct data |
| `testEmptyNetwork` | Throws `RequiredNetworkNameException` |
| `testEmptyIdentity` | Throws `RequiredNetworkIdentityException` |
| `testIsForNetwork` | Returns true for matching network name |
| `testIsForNetworkFalse` | Returns false for different network |

### Unit/ValueObject/ResetTokenTest.php

| Test Case | Description |
|-----------|-------------|
| `testCreate` | Token created with value and expiration |
| `testEmptyToken` | Throws exception for empty token |
| `testIsExpired` | Returns true when date >= expiresAt |
| `testNotExpired` | Returns false when date < expiresAt |
| `testGetToken` | Returns token string |

### Unit/Event/UserSignedUpByEmailTest.php

| Test Case | Description |
|-----------|-------------|
| `testCreate` | Event created with userId and email |
| `testAggregateId` | Returns userId |
| `testToArray` | Returns array with userId and email |
| `testOccurredOn` | Returns DateTimeImmutable |

### Unit/Event/UserSignedUpByNetworkTest.php

| Test Case | Description |
|-----------|-------------|
| `testCreate` | Event created with all data |
| `testWithNullEmail` | Email can be null |
| `testAggregateId` | Returns userId |
| `testToArray` | Returns complete array |

### Unit/Command/SignUpByEmailHandlerTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | User created and persisted |
| `testEmailAlreadyExists` | Throws exception if email exists |
| `testEventDispatched` | Domain event is dispatched |
| `testMailerCalled` | Confirmation email is sent |

### Unit/Command/SignUpByNetworkHandlerTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | User created with network |
| `testNetworkAlreadyExists` | Throws exception if network exists |
| `testEventDispatched` | Domain event is dispatched |

### Unit/Command/ConfirmSignUpHandlerTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | User confirmed successfully |
| `testInvalidToken` | Throws `IncorrectTokenException` |

### Unit/Command/RequestPasswordResetHandlerTest.php

| Test Case | Description |
|-----------|-------------|
| `testSuccess` | Reset token created |
| `testUserNotFound` | Throws exception if user not found |

### Unit/Services/ResetTokenizerTest.php

| Test Case | Description |
|-----------|-------------|
| `testGenerate` | Returns ResetToken instance |
| `testExpiresAtCalculation` | ExpiresAt = now + interval |
| `testTokenIsUuid` | Token is valid UUID |

### Integration/Repository/UserRepositoryTest.php

| Test Case | Description |
|-----------|-------------|
| `testAdd` | User persisted to database |
| `testFindByConfirmToken` | Returns user by token |
| `testFindByConfirmTokenNotFound` | Returns null if not found |
| `testHasByNetworkIdentity` | Returns true if network exists |
| `testHasByNetworkIdentityNotFound` | Returns false if not exists |
| `testGetByEmail` | Returns user by email |
| `testGetByEmailNotFound` | Returns null if not found |

### Functional/Http/SignUpControllerTest.php

| Test Case | Description |
|-----------|-------------|
| `testSignUpSuccess` | Returns 201, user created |
| `testSignUpInvalidEmail` | Returns 400 with error |
| `testSignUpEmailExists` | Returns 400 with error |
| `testSignUpEmptyPassword` | Returns 400 with validation error |

### Functional/Http/ConfirmControllerTest.php

| Test Case | Description |
|-----------|-------------|
| `testConfirmSuccess` | Returns 200, user activated |
| `testConfirmInvalidToken` | Returns 400 with error |

## Test Priority

1. **Unit/ValueObject/** - Simple, isolated, start here
2. **Unit/Entity/** - Core business logic
3. **Unit/Event/** - Domain events
4. **Unit/Command/** - Handlers with mocks
5. **Integration/Repository/** - Requires test database
6. **Functional/Http/** - End-to-end tests

## Running Tests

```bash
# Run all tests
./bin/phpunit

# Run specific suite
./bin/phpunit --testsuite Unit
./bin/phpunit --testsuite Integration
./bin/phpunit --testsuite Functional

# Run specific test file
./bin/phpunit src/User/Infrastructure/Tests/Unit/Entity/UserSignUpByEmailTest.php

# Run with coverage
./bin/phpunit --coverage-html coverage/
```

## Total: ~25 Test Classes

| Type | Count |
|------|-------|
| Unit (Shared) | 5 |
| Unit (User) | 14 |
| Integration | 1 |
| Functional | 2 |
| **Total** | **22** |