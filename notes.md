Три таблицы: пользователи, кафе, заказы пользователей в кафе
users: id PK, user_name
cafe: id PK, cafe_name -> seed
orders: id PK, cafe_id, user_id - флаги и мусор

+регистрация 
   - аоинкремент id
   - возвращает jwt (?)
   - возвращает id:
     - payload
     - redirect()

pages:
    - register
    - login
    - /orders
    - /newOrder

newOrder:
    - давить string к выбранному кафе из списка
    - может быть флаг

login
    - redirect /orders

уязвимость:
    - id в токене - автоинкремент
    - дефолтная подпись токена + нет проверки на Null
    

токен:
    - base64encode/decode
    - самостоятельная реализация. берется массив, енкодится, конкатенируется с подписью
