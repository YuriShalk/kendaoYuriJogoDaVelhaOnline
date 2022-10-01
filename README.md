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

```jsonc
Request:

{
    "username": "kendao"
}
```

```jsonc
Responses:

// Status 201
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "kendao",
    "wins": 0,
    "losses": 0
}

// Status 409
{ "status": 409, "error": "username already exists" }
```

---

Obter dados do usuário

`GET http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42`

```jsonc
Responses:

// Status 200
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "kendao",
    "wins": 0,
    "losses": 0
}

// Status 404
{ "status": 404, "error": "account not found" }
```

---

Atualizar usuário

`PATCH http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42`

```jsonc
Request:

{
    "username": "joão"
}
```

```jsonc
Responses:

// Status 200
{
    "id": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42",
    "username": "joão",
    "wins": 0,
    "losses": 0
}

// Status 404
{ "status": 404, "error": "account not found" }

// Status 409
{ "status": 409, "error": "username already exists" }
```

---

Deletar usuário

`DELETE http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42`

```jsonc
Responses:

// Status 204
(empty body)

// Status 404
{ "status": 404, "error": "account not found" }
```

---

Criar jogo

`POST http://kg-azevedo.ml/api/tictactoe/games/create`

```jsonc
Request:

{
    "id_player": "b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42"
}
```

```jsonc
Responses:

// Status 201
{
    "id": 1,
    "turn": "GUEST",
    "status": "CREATED",
    "owner": {
        "username": "kendao",
        "wins": 0,
        "losses": 0,
        "myself": true
    }
}

// Status 400
{ "status": 400, "error": "bad request" }

// Status 422
{ "status": 422, "error": "player does not exist" }
```

---

Entrar no jogo

`POST http://kg-azevedo.ml/api/tictactoe/games/1/join`

```jsonc
Request:

{
    "id_player": "51c0165f-40ec-11ed-a4a5-ac1f6b8b5c42"
}
```

```jsonc
Responses:

// Status 200
{
    "id": 1,
    "turn": "GUEST",
    "status": "STARTED",
    "owner": {
        "username": "kendao",
        "wins": 0,
        "losses": 0,
        "myself": false
    },
    "guest": {
        "username": "yuri",
        "wins": 0,
        "losses": 0,
        "myself": true
    }
}

// Status 400
{ "status": 400, "error": "bad request" }

// Status 404
{ "status": 404, "error": "game not found" }

// Status 422
{ "status": 422, "error": "player does not exist" }
{ "status": 422, "error": "game is done" }
{ "status": 422, "error": "game is full" }
```

---

Jogar partida

`POST http://kg-azevedo.ml/api/tictactoe/games/1/play`

```jsonc
Request:

{
    "id_player": "51c0165f-40ec-11ed-a4a5-ac1f6b8b5c42",
    "position": 5
}
```

```jsonc
Responses:

// Status 200
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
        "losses": 0,
        "myself": false
    },
    "guest": {
        "username": "yuri",
        "wins": 0,
        "losses": 0,
        "myself": true
    }
}

// Status 400
{ "status": 400, "error": "bad request" }

// Status 404
{ "status": 404, "error": "game not found" }

// Status 409
{ "status": 409, "error": "position already marked" }

// Status 422
{ "status": 422, "error": "game is done" }
{ "status": 422, "error": "game is not started" }
{ "status": 422, "error": "invalid turn" }
```

---

Obter dados do jogo

`GET http://kg-azevedo.ml/api/tictactoe/games/1?id_player=b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42`

```jsonc
Responses:

// Status 200
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
        "losses": 0,
        "myself": true
    },
    "guest": {
        "username": "yuri",
        "wins": 0,
        "losses": 1,
        "myself": false
    },
    "winner": {
        "username": "kendao",
        "wins": 1,
        "losses": 0,
        "myself": true
    }
}

// Status 404
{ "status": 404, "error": "game not found" }
```

# Regras

Etapa 1 - Criação de Usuário:
- Antes de tudo, é necessário a criação de um **usuário** para começar a jogar online. O front deverá verificar se existe algum id de usuário no próprio _localStorage_. Se já existir, ok. Se não existir, será necessário chamar a API `POST /accounts` informando um nome de usuário, capturar o id dele na resposta da API e armazenar este id no _localStorage_. Caso o front queira exibir os dados do usuário (nome, ganhos e percas), basta ele chamar a API `GET /accounts/{id}` informando o id do usuário. OBS: Pode ser que o nome de usuário já esteja cadastrado para outro jogador e a API retorne erro (status 409). OBS²: _Usuário_ e _jogador_ são a mesma coisa.

Etapa 2 - Criação de Jogo:
- O front deverá disponibilizar a opção pro usuário criar uma sala e jogar online com outro jogador. Para isso, o front deverá chamar a API `POST /games/create` informando o id do usuário. Logo após, o front deverá redirecionar este usuário para a sala criada e fazer com que ele fique aguardando a entrada de um outro jogador. OBS: O usuário deverá informar o id do jogo retornado pela API para outro jogador, para que ambos fiquem na mesma sala. OBS²: O id do jogo e o id da sala são a mesma coisa.

Etapa 3 - Entrar no Jogo:
- O front deverá disponibilizar a opção pro usuário entrar em uma sala já existente informando o id (ou código) dela. Para isso, o front deverá chamar a API `POST /games/{id}/join`, informando também o id do usuário. Logo após, o front deverá redirecionar o usuário para a sala existente para que o jogo comece. OBS: Pode ser que a sala informada pelo usuário não exista e a API retorne erro (404).

Etapa 4 - Obter Dados do Jogo:
- O front deverá obter os dados do jogo de tempos em tempos (polling) para saber: as posições preenchidas dos _X/O_, qual é a vez de qual jogador, se o jogo já foi finalizado, quem foi o vencedor etc. Para isso, basta chamar a API `GET /games/{id}?id_player={id_player}`, que será retornado todos estes dados.

Etapa 5 - Realizar Jogada:
- O front deverá disponibilizar a opção para o usuário jogar a sua partida (caso seja o seu turno). Para isso, o front deverá chamar a API `POST /games/{id}/play` informando o id da sala, o id do jogador e a posição que ele deseja jogar. Caso o jogo tenha sido finalizado, o status dele será alterado para _DONE_ e os ganhos/percas dos usuários serão incrementados. OBS: Por padrão, o criador da sala recebe o _X_ e o visitante recebe o _O_. OBS2: As posições são de 1 até 9, conforme o exemplo abaixo:

```
1 | 2 | 3
--+---+--
4 | 5 | 6
--+---+--
7 | 8 | 9
```

# Contribuidores
- Kenneth Gottschalk de Azevedo
- Yuri Gottschalk de Azevedo
