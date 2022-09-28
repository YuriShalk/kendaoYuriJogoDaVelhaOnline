# Tic Tac Toe
Este projeto é um jogo da velha online/offline.

# Frontend

## Execução

Para executar o projeto em sua máquina, basta abrir um dos arquivos .html contidos na pasta front.

# Backend

## Execução

Para executar o projeto em sua máquina, é necessário possuir o Laraval instalado nela.

## Rotas disponíveis

GET http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42

```json
Responses:

Status 200
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "kendao",
    "updated_at": "2022-09-28 10:01:40",
    "created_at": "2022-09-28 10:01:40"
}

Status 404
account not found
```

POST http://kg-azevedo.ml/api/tictactoe/accounts

```json
Request:

{
    "username": "kendao"
}
```

```json
Responses:

Status 201
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "kendao",
    "updated_at": "2022-09-28 10:01:40",
    "created_at": "2022-09-28 10:01:40"
}

Status 409
account already exists
```

PATCH http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42

```json
Request:

{
    "username": "kendao2"
}
```

```json
Responses:

Status 200
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "kendao2",
    "updated_at": "2022-09-28 10:01:40",
    "created_at": "2022-09-28 10:01:40"
}

Status 409
account already exists
```

DELETE http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42

```json
Responses:

Status 204

Status 404
account not found
```

# Contribuidores
- Kenneth Gottschalk de Azevedo
- Yuri Gottschalk de Azevedo
