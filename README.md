# ZPD-training-service
Service for A/D CTF training on golang.

## cafeteria

Service for storing orders in some pre-defind cafes

### Tags

- php
- postgres
- web

### Vulnerabilities

- Wrong SQL query used while /login. With knowing that you can add second user to DB and login as first, not knowing the password
- user_id are serial generated
- in /debug there is a JWT_key set inside the project container.

## Deploy

### Service

```bash
cd ./services/cafeteria
docker-compose up -d
```

### Checker

The checker interface matches the description for ructf: `https://github.com/HackerDom/ructf-2017/wiki/Интерфейс-«проверяющая-система-чекеры»`

```bash
cd ./checkers/fortuneteller
python3 checker.py 
```

To use it with ructf jury, you need to change the output format of the checker `info` function:
- comment this row https://github.com/seemenkina/fortuneteller/blob/master/checkers/fortuneteller/checker.py#L457
- delete comment from this row https://github.com/seemenkina/fortuneteller/blob/master/checkers/fortuneteller/checker.py#L458


## Contributors

@cinsort

