<script setup>
const sections = [
    {
        title: 'Авторизация',
        endpoints: [
            {
                method: 'POST',
                path: '/api/login',
                status: '200 OK',
                auth: false,
                description: 'Проверяет логин и пароль и возвращает Bearer Token.',
                fields: [
                    ['email', 'string, email', 'да', 'Email пользователя.'],
                    ['password', 'string', 'да', 'Пароль пользователя.'],
                ],
                request: `POST /api/login HTTP/1.1
Accept: application/json
Content-Type: application/json

{
  "email": "user@example.com",
  "password": "password"
}`,
                response: `{
  "token": "1|example-bearer-token"
}`,
                note: 'При неверном логине или пароле API возвращает 401 и {"message":"Invalid credentials"}.',
            },
            {
                method: 'GET',
                path: '/api/user',
                status: '200 OK',
                auth: true,
                description: 'Возвращает данные текущего авторизованного пользователя.',
                request: `GET /api/user HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: `{
  "id": 1,
  "name": "Test User",
  "email": "user@example.com",
  "email_verified_at": null,
  "created_at": "2026-11-27T10:00:00.000000Z",
  "updated_at": "2026-11-27T10:00:00.000000Z"
}`,
            },
            {
                method: 'POST',
                path: '/api/logout',
                status: '204 No Content',
                auth: true,
                description: 'Удаляет текущий Bearer Token.',
                request: `POST /api/logout HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: 'Тело ответа отсутствует.',
            },
        ],
    },
    {
        title: 'Пресеты',
        endpoints: [
            {
                method: 'GET',
                path: '/api/presets',
                status: '200 OK',
                auth: true,
                description: 'Возвращает список пресетов с пагинацией, количеством и общей стоимостью продуктов.',
                fields: [
                    ['page', 'integer', 'нет', 'Номер страницы, по умолчанию 1.'],
                    ['pagintaion_size', 'integer', 'нет', 'Количество записей, по умолчанию 10.'],
                    ['sort_field', 'string', 'нет', 'Поле сортировки: id, name, status, preset_price, products_count или products_sum.'],
                    ['sort_direction', 'asc | desc', 'нет', 'Направление сортировки.'],
                    ['filters', 'array', 'нет', 'Массив фильтров. Для статуса используется field=status и value=0 или 1.'],
                ],
                request: `GET /api/presets?page=1&pagintaion_size=10&sort_field=preset_price&sort_direction=asc&filters[0][field]=status&filters[0][value]=0 HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: `{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Preset1",
      "status": 0,
      "preset_price": 1500,
      "user_id": null,
      "created_at": "2026-11-27T10:00:00.000000Z",
      "updated_at": "2026-11-27T10:00:00.000000Z",
      "products_count": 2,
      "products_sum": "1250.00"
    }
  ],
  "per_page": 10,
  "total": 1
}`,
                note: 'Ответ также содержит стандартные ссылки Laravel: first_page_url, last_page_url, links, next_page_url и prev_page_url.',
            },
            {
                method: 'GET',
                path: '/api/presets/{preset}',
                status: '200 OK',
                auth: true,
                description: 'Возвращает один пресет и список входящих в него продуктов.',
                request: `GET /api/presets/1 HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: `{
  "data": {
    "id": 1,
    "name": "Preset1",
    "status": 0,
    "preset_price": 1500,
    "user_id": null,
    "created_at": "2026-11-27T10:00:00.000000Z",
    "updated_at": "2026-11-27T10:00:00.000000Z",
    "products": [
      {
        "id": 1,
        "name": "Product1",
        "price": "500.00",
        "composition": "Material1",
        "preset_id": 1,
        "created_at": "2026-11-27T09:00:00.000000Z",
        "updated_at": "2026-11-27T10:00:00.000000Z"
      }
    ]
  }
}`,
            },
            {
                method: 'POST',
                path: '/api/presets',
                status: '201 Created',
                auth: true,
                description: 'Создаёт пресет и при необходимости привязывает к нему свободные продукты.',
                permission: 'Только пользователь manager@example.com.',
                fields: [
                    ['name', 'string, alpha_num', 'да', 'Название из букв и цифр без пробелов.'],
                    ['preset_price', 'numeric', 'да', 'Цена пресета.'],
                    ['product_ids', 'integer[]', 'нет', 'Уникальные ID существующих продуктов.'],
                ],
                request: `POST /api/presets HTTP/1.1
Accept: application/json
Content-Type: application/json
Authorization: Bearer <token>

{
  "name": "Preset1",
  "preset_price": 1500,
  "product_ids": [1, 2]
}`,
                response: `{
  "data": {
    "name": "Preset1",
    "preset_price": 1500,
    "updated_at": "2026-11-27T10:00:00.000000Z",
    "created_at": "2026-11-27T10:00:00.000000Z",
    "id": 1,
    "products": [
      {
        "id": 1,
        "name": "Product1",
        "price": "500.00",
        "composition": "Material1",
        "preset_id": 1,
        "created_at": "2026-11-27T09:00:00.000000Z",
        "updated_at": "2026-11-27T10:00:00.000000Z"
      }
    ]
  }
}`,
                note: 'Поля status и user_id передавать запрещено. Один продукт может принадлежать только одному пресету.',
            },
            {
                method: 'PATCH',
                path: '/api/presets/{preset}',
                status: '200 OK',
                auth: true,
                description: 'Обновляет переданные поля пресета. product_ids полностью заменяет текущий список продуктов.',
                permission: 'Только пользователь manager@example.com.',
                fields: [
                    ['name', 'string', 'нет', 'Новое название.'],
                    ['preset_price', 'numeric', 'нет', 'Новая цена.'],
                    ['product_ids', 'integer[]', 'нет', 'Новый список связанных продуктов; [] удаляет все связи.'],
                ],
                request: `PATCH /api/presets/1 HTTP/1.1
Accept: application/json
Content-Type: application/json
Authorization: Bearer <token>

{
  "preset_price": 1750,
  "product_ids": [2, 3]
}`,
                response: `{
  "data": {
    "id": 1,
    "name": "Preset1",
    "status": 0,
    "preset_price": 1750,
    "user_id": null,
    "created_at": "2026-11-27T10:00:00.000000Z",
    "updated_at": "2026-11-27T11:00:00.000000Z",
    "products": [
      {
        "id": 2,
        "name": "Product2",
        "price": "600.00",
        "composition": "Material2",
        "preset_id": 1,
        "created_at": "2026-11-27T09:00:00.000000Z",
        "updated_at": "2026-11-27T11:00:00.000000Z"
      },
      {
        "id": 3,
        "name": "Product3",
        "price": "650.00",
        "composition": "Material3",
        "preset_id": 1,
        "created_at": "2026-11-27T09:00:00.000000Z",
        "updated_at": "2026-11-27T11:00:00.000000Z"
      }
    ]
  }
}`,
            },
            {
                method: 'DELETE',
                path: '/api/presets/{preset}',
                status: '204 No Content',
                auth: true,
                description: 'Удаляет пресет. У связанных продуктов preset_id становится null.',
                request: `DELETE /api/presets/1 HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: 'Тело ответа отсутствует.',
            },
            {
                method: 'POST',
                path: '/api/presets/{id}/book',
                status: '200 OK',
                auth: true,
                description: 'Бронирует свободный пресет за текущим пользователем.',
                request: `POST /api/presets/1/book HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: `{
  "message": "Пресет успешно забронирован",
  "status": 200
}`,
                note: 'Если пресет уже забронирован, API возвращает 409 Conflict.',
            },
        ],
    },
    {
        title: 'Продукты',
        endpoints: [
            {
                method: 'GET',
                path: '/api/products',
                status: '200 OK',
                auth: true,
                description: 'Возвращает список продуктов с пагинацией и сортировкой.',
                fields: [
                    ['page', 'integer', 'нет', 'Номер страницы, по умолчанию 1.'],
                    ['pagintaion_size', 'integer', 'нет', 'Количество записей, по умолчанию 10.'],
                    ['sort_field', 'string', 'нет', 'Поле сортировки: id, name, price или preset_id.'],
                    ['sort_direction', 'asc | desc', 'нет', 'Направление сортировки.'],
                ],
                request: `GET /api/products?page=1&pagintaion_size=10&sort_field=price&sort_direction=asc HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: `{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "name": "Product1",
      "price": "500.00",
      "composition": "Material1",
      "preset_id": null,
      "created_at": "2026-11-27T10:00:00.000000Z",
      "updated_at": "2026-11-27T10:00:00.000000Z"
    }
  ],
  "per_page": 10,
  "total": 1
}`,
                note: 'Ответ также содержит стандартные ссылки пагинации Laravel.',
            },
            {
                method: 'POST',
                path: '/api/products',
                status: '201 Created',
                auth: true,
                description: 'Создаёт новый продукт.',
                permission: 'Только пользователь manager@example.com.',
                fields: [
                    ['name', 'string, alpha_num', 'да', 'Название из букв и цифр без пробелов.'],
                    ['price', 'numeric', 'да', 'Цена продукта.'],
                    ['composition', 'string, alpha_num', 'да', 'Состав из букв и цифр без пробелов.'],
                ],
                request: `POST /api/products HTTP/1.1
Accept: application/json
Content-Type: application/json
Authorization: Bearer <token>

{
  "name": "Product1",
  "price": 500,
  "composition": "Material1"
}`,
                response: `{
  "data": {
    "name": "Product1",
    "price": 500,
    "composition": "Material1",
    "updated_at": "2026-11-27T10:00:00.000000Z",
    "created_at": "2026-11-27T10:00:00.000000Z",
    "id": 1
  }
}`,
            },
            {
                method: 'PATCH',
                path: '/api/products/{product}',
                status: '200 OK',
                auth: true,
                description: 'Обновляет только переданные поля продукта.',
                permission: 'Только пользователь manager@example.com.',
                fields: [
                    ['name', 'string, alpha_num', 'нет', 'Новое название.'],
                    ['price', 'numeric', 'нет', 'Новая цена.'],
                    ['composition', 'string, alpha_num', 'нет', 'Новый состав.'],
                ],
                request: `PATCH /api/products/1 HTTP/1.1
Accept: application/json
Content-Type: application/json
Authorization: Bearer <token>

{
  "price": 550
}`,
                response: `{
  "data": {
    "id": 1,
    "name": "Product1",
    "price": 550,
    "composition": "Material1",
    "preset_id": null,
    "created_at": "2026-11-27T10:00:00.000000Z",
    "updated_at": "2026-11-27T11:00:00.000000Z"
  }
}`,
            },
            {
                method: 'DELETE',
                path: '/api/products/{product}',
                status: '204 No Content',
                auth: true,
                description: 'Удаляет продукт.',
                request: `DELETE /api/products/1 HTTP/1.1
Accept: application/json
Authorization: Bearer <token>`,
                response: 'Тело ответа отсутствует.',
            },
        ],
    },
];

const errorStatuses = [
    ['401 Unauthorized', 'Bearer Token отсутствует, недействителен или логин/пароль неверны.'],
    ['404 Not Found', 'Пресет или продукт не найден.'],
    ['409 Conflict', 'Пресет уже забронирован.'],
    ['422 Unprocessable Content', 'Данные запроса не прошли валидацию.'],
    ['500 Internal Server Error', 'Внутренняя ошибка сервера.'],
];

const validationError = `{
  "type": "about:blank",
  "title": "Ошибка валидации",
  "status": 422,
  "detail": "Переданные данные не прошли проверку.",
  "instance": "/api/products",
  "errors": {
    "name": ["The name field is required."]
  }
}`;
</script>

<template>
    <div class="main">
        <div class="mb-4 flex items-center justify-between">
            <h1 class="font-semibold">Документация API</h1>
            <RouterLink to="/home" class="underline">На главную</RouterLink>
        </div>

        <div class="mb-6">
            <p><strong>Базовый путь:</strong> <code>/api</code></p>
            <p><strong>Формат данных:</strong> <code>application/json</code></p>
            <p>
                Все запросы, кроме <code>POST /api/login</code>, требуют заголовок
                <code>Authorization: Bearer &lt;token&gt;</code>.
            </p>
        </div>

        <section v-for="section in sections" :key="section.title" class="mb-8">
            <h2 class="mb-3 text-xl font-semibold">{{ section.title }}</h2>

            <details
                v-for="endpoint in section.endpoints"
                :key="`${endpoint.method}-${endpoint.path}`"
                class="mb-4 border p-3 shadow-xs"
            >
                <summary class="mb-2 flex cursor-pointer flex-wrap items-center justify-between gap-2 select-none">
                    <h3 class="font-semibold">
                        <code>{{ endpoint.method }} {{ endpoint.path }}</code>
                    </h3>
                    <span>{{ endpoint.status }}</span>
                </summary>

                <p class="mb-2">{{ endpoint.description }}</p>
                <p class="mb-3">
                    <strong>Авторизация:</strong>
                    {{ endpoint.auth ? 'Bearer Token' : 'не требуется' }}
                </p>
                <p v-if="endpoint.permission" class="mb-3">
                    <strong>Доступ:</strong> {{ endpoint.permission }}
                </p>

                <div v-if="endpoint.fields" class="mb-3 overflow-x-auto">
                    <table class="w-full shadow-xs">
                        <thead>
                            <tr>
                                <th class="border p-1 text-left">Параметр</th>
                                <th class="border p-1 text-left">Тип</th>
                                <th class="border p-1 text-left">Обязательный</th>
                                <th class="border p-1 text-left">Описание</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="field in endpoint.fields" :key="field[0]">
                                <td class="border p-1"><code>{{ field[0] }}</code></td>
                                <td class="border p-1">{{ field[1] }}</td>
                                <td class="border p-1">{{ field[2] }}</td>
                                <td class="border p-1">{{ field[3] }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <h4 class="font-semibold">Пример запроса</h4>
                <pre class="mb-3 overflow-x-auto border p-2"><code>{{ endpoint.request }}</code></pre>

                <h4 class="font-semibold">Пример ответа — {{ endpoint.status }}</h4>
                <pre class="overflow-x-auto border p-2"><code>{{ endpoint.response }}</code></pre>

                <p v-if="endpoint.note" class="mt-2"><strong>Примечание:</strong> {{ endpoint.note }}</p>
            </details>
        </section>

        <section class="mb-8">
            <h2 class="mb-3 text-xl font-semibold">Ошибки</h2>
            <table class="mb-3 w-full shadow-xs">
                <thead>
                    <tr>
                        <th class="border p-1 text-left">Статус</th>
                        <th class="border p-1 text-left">Описание</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="error in errorStatuses" :key="error[0]">
                        <td class="border p-1"><code>{{ error[0] }}</code></td>
                        <td class="border p-1">{{ error[1] }}</td>
                    </tr>
                </tbody>
            </table>

            <p class="mb-1">Большинство ошибок возвращается с типом <code>application/problem+json</code>:</p>
            <pre class="overflow-x-auto border p-2"><code>{{ validationError }}</code></pre>
        </section>
    </div>
</template>
