# Tournament API — Laravel + Symfony (лабораторна робота)

Дві окремі реалізації одного й того ж CRUD API для сутності **Tournament** (турнір): одна на Laravel (Eloquent), друга на Symfony (Doctrine ORM). Обидві використовують SQLite, тож піднімати окрему СУБД не потрібно.

## Структура репозиторію

```
frameworks/
├── laravel/                          # Laravel API
├── symfony/                          # Symfony API
└── Tournament-API.postman_collection.json   # готова колекція запитів для тестування
```

## Вимоги

- PHP 8.2+
- Composer
- Postman (або curl/будь-який HTTP-клієнт) — для тестування

## Швидкий старт (проєкт уже підготовлений)

Залежності (`vendor/`) та SQLite-бази вже встановлені й заповнені тестовими даними, тож достатньо просто піднятие сервери. **Важливо запускати обидва одночасно** (у двох окремих терміналах), бо саме на ці порти налаштована колекція Postman:

### Symfony → http://localhost:8000

```bash
cd symfony
php -S 127.0.0.1:8000 -t public
```

### Laravel → http://localhost:8001

```bash
cd laravel
php artisan serve --port=8001
```

(Symfony CLI у системі не встановлено, тому замість `symfony server:start` використовується вбудований PHP-сервер — для цілей лабораторної цього достатньо.)

## Встановлення "з нуля" (якщо клонували репозиторій вперше)

`vendor/` і файли SQLite-баз не зберігаються в git, тож перед першим запуском потрібно:

### Symfony

```bash
cd symfony
composer install
php bin/console doctrine:database:create      # якщо файл бази ще не існує
php bin/console doctrine:migrations:migrate --no-interaction
```

### Laravel

```bash
cd laravel
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --force
```

Після цього запускайте сервери так, як у розділі «Швидкий старт».

## Тестування через Postman

1. Імпортуйте `Tournament-API.postman_collection.json` у Postman.
2. У колекції вже задані змінні (Collection → Variables):
   - `symfony_base_url` = `http://localhost:8000`
   - `laravel_base_url` = `http://localhost:8001/api`

   Переконайтеся, що вони збігаються з портами, на яких реально запущені сервери.
3. У колекції дві групи запитів (Symfony / Laravel), у кожній — однаковий набір CRUD-операцій:

   | Операція | Метод | Шлях |
   |---|---|---|
   | List Tournaments | GET | `/tournaments` |
   | Get Tournament | GET | `/tournaments/:id` |
   | Create Tournament | POST | `/tournaments` |
   | Update Tournament | PATCH | `/tournaments/:id` |
   | Delete Tournament | DELETE | `/tournaments/:id` |

4. Рекомендований порядок перевірки: **List → Create → Get (підставити `id` з відповіді Create) → Update → Delete → List** (переконатися, що запис зник).

### Різниця у форматі полів

- **Symfony** очікує camelCase: `name`, `location`, `startDate`, `maxTeams`
- **Laravel** очікує snake_case: `name`, `location`, `start_date`, `max_teams`

(Приклади тіла запиту вже прописані в самій колекції Postman.)

## Тестування без Postman (curl)

```bash
# Symfony — список турнірів
curl http://localhost:8000/api/tournaments

# Symfony — створення турніру
curl -X POST http://localhost:8000/api/tournaments \
  -H "Content-Type: application/json" \
  -d '{"name":"Champions Cup","location":"Kyiv","startDate":"2026-09-01","maxTeams":16}'

# Laravel — список турнірів
curl http://localhost:8001/api/tournaments

# Laravel — створення турніру
curl -X POST http://localhost:8001/api/tournaments \
  -H "Content-Type: application/json" -H "Accept: application/json" \
  -d '{"name":"Champions Cup","location":"Lviv","start_date":"2026-09-01","max_teams":16}'
```

Для `Get` / `Update` / `Delete` аналогічно, додаючи `/{id}` до шляху (методи `GET` / `-X PATCH` / `-X DELETE`).

## Автоматизовані тести (PHPUnit)

```bash
cd laravel && php artisan test     # стандартні приклади Laravel (ExampleTest), власних тестів на Tournament немає
cd symfony && php bin/phpunit      # тестових класів ще немає — команда повідомить, що тестів не знайдено
```

Це нормально: основний спосіб перевірки функціоналу в цій роботі — колекція Postman, а не автотести.

## Можливі проблеми

- **Порт зайнятий** — запустіть на іншому порту (`php artisan serve --port=8002` / `php -S 127.0.0.1:8002 -t public`) і поправте відповідну змінну `*_base_url` у Postman.
- **"Database file does not exist" (Laravel)** — переконайтеся, що файл `laravel/database/database.sqlite` створено (див. розділ «Встановлення з нуля»).
- **Порожня відповідь / 500 (Symfony)** — перевірте, що міграції виконано (`php bin/console doctrine:migrations:migrate`).
