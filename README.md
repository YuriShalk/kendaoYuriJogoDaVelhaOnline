# Tic Tac Toe
Este projeto é um jogo da velha online/offline.

# Frontend

## Execução

Para executar o projeto em sua máquina, basta abrir o arquivo [index.html](/front/index.html) em algum navegador.

# Backend

## Execução

Para executar o projeto em sua máquina, é necessário instalar o Laraval 5 e adicionar os arquivos da pasta [back](/back/).

## Banco de dados

Este projeto utiliza o banco de dados MySql:

![Banco de Dados](/back/database/designer.png)

A tabela `tictactoe_account` é a tabela de usuários:

- id = identificador do usuário (uuid);
- username = nome do usuário (único);
- password = senha do usuário (não utilizado);
- wins = quantidade de jogos ganhos;
- losses = quantidade de jogos perdidos;
- created_at = data de criação do usuário;
- updated_at = data da última atualização do usuário;

A tabela `tictactoe_game` é a tabela de jogos:

- id = identificador/código do jogo;
- id_owner = id do criador do jogo (uuid);
- id_guest = id do oponente (uuid);
- id_winner = id do ganhador do jogo (uuid);
- first_position = valor da primeira posição (X/O/null);
- second_position = valor da segunda posição (X/O/null);
- third_position = valor da terceira posição (X/O/null);
- fourth_position = valor da quarta posição (X/O/null);
- fifth_position = valor da quinta posição (X/O/null);
- sixth_position = valor da sexta posição (X/O/null);
- seventh_position = valor da sétima posição (X/O/null);
- eighth_position = valor da oitava posição (X/O/null);
- nineth_position = valor da nona posição (X/O/null);
- turn = especifica o turno do atual jogador (OWNER/GUEST);
- status = status do jogo (CREATED/STARTED/DONE);
- created_at = data de criação do jogo;
- updated_at = data da última atualização do jogo;

## Rotas da API

Criar usuário

`POST http://kg-azevedo.ml/api/tictactoe/accounts`

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
    "wins": 0,
    "losses": 0
}

Status 409
username already exists
```

---

Obter usuário

`GET http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42`

```json
Responses:

Status 200
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "kendao",
    "wins": 0,
    "losses": 0
}

Status 404
account not found
```

---

Atualizar usuário

`PATCH http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42`

```json
Request:

{
    "username": "yuri"
}
```

```json
Responses:

Status 200
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "yuri",
    "wins": 0,
    "losses": 0
}

Status 409
username already exists
```

---

Deletar usuário

`DELETE http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42`

```json
Responses:

Status 204
(corpo vazio)

Status 404
account not found
```

---

Criar jogo

`POST http://kg-azevedo.ml/api/tictactoe/games/create`

```json
Request:

{
    "id_player": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42"
}
```

```json
Responses:

Status 201
{
    "id": 1,
    "turn": "GUEST",
    "status": "CREATED",
    "owner": {
        "username": "kendao",
        "wins": 0,
        "losses": 0
    }
}

Status 400
bad request

Status 422
player not found
```

---

Entrar no jogo

`POST http://kg-azevedo.ml/api/tictactoe/games/1/join`

```json
Request:

{
    "id_player": "51c0165f-40ec-11ed-a4a5-ac1f6b8b5c42"
}
```

```json
Responses:

Status 200
{
    "id": 1,
    "turn": "GUEST",
    "status": "STARTED",
    "owner": {
        "username": "kendao",
        "wins": 0,
        "losses": 0
    },
    "guest": {
        "username": "yuri",
        "wins": 0,
        "losses": 0
    }
}

Status 400
bad request

Status 404
game not found

Status 422
player not found
game is done
game is full
```

---

Jogar partida

`POST http://kg-azevedo.ml/api/tictactoe/games/1/play`

```json
Request:

{
    "id_player": "51c0165f-40ec-11ed-a4a5-ac1f6b8b5c42",
    "position": 5
}
```

```json
Responses:

Status 200
{
    "id": 8,
    "first_position": null,
    "second_position": null,
    "third_position": null,
    "fourth_position": null,
    "fifth_position": "O",
    "sixth_position": null,
    "seventh_position": null,
    "eighth_position": null,
    "nineth_position": null,
    "turn": "OWNER",
    "status": "STARTED",
    "owner": {
        "username": "kendao",
        "wins": 0,
        "losses": 0
    },
    "guest": {
        "username": "yuri",
        "wins": 0,
        "losses": 0
    }
}

Status 400
bad request

Status 404
game not found

Status 409
position already marked

Status 422
game is done
game is not started
invalid turn
```

---

Obter jogo

`GET http://kg-azevedo.ml/api/tictactoe/games/1`

```json
Responses:

Status 200
{
    "id": 1,
    "first_position": "X",
    "second_position": "X",
    "third_position": "X",
    "fourth_position": "O",
    "fifth_position": "O",
    "sixth_position": null,
    "seventh_position": "0",
    "eighth_position": null,
    "nineth_position": null,
    "turn": "OWNER",
    "status": "DONE",
    "owner": {
        "username": "kendao",
        "wins": 1,
        "losses": 0
    },
    "guest": {
        "username": "yuri",
        "wins": 0,
        "losses": 1
    },
    "winner": {
        "username": "kendao",
        "wins": 1,
        "losses": 0
    }
}

Status 404
game not found
```

# Contribuidores
- Kenneth Gottschalk de Azevedo
- Yuri Gottschalk de Azevedo
