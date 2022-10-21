// Etapa 1:

// dados a serem enviados pela solicitação POST
getInputUsername().focus();

const url = 'http://kg-azevedo.ml/api/tictactoe';

function criaUsuario() {

    var nomeUsuario = getInputUsername();

    const requestBody = {  // objeto
        username: nomeUsuario.value
    };
    
    fetch(url + '/accounts', {
        'method': "POST",
        'body': JSON.stringify(requestBody),
        headers: { "Content-type": "application/json; charset=UTF-8" }
    })
        .then(responseObj => {
            console.log('status da api: ' + responseObj.status);
            if (responseObj.ok) { // status de sucesso (entre 200 e 299)
                responseObj.json().then(responseData => {
                    localStorage.setItem('idUsuario', responseData.id);
                    localStorage.setItem('idNome', responseData.username);

                    alert('deu bom!');
                });
            } else { // status de erro (entre 400 e 599)
                if (responseObj.status == 409) {
                    alert('esse nome de usuario ja existe');
                } else {
                    responseObj.text().then(text => {
                        alert('deu ruim: ' + text);
                    });
                }
            }
        })
        .catch(err => {
            console.error(err);
        });
}

function getInputUsername() {
    return document.getElementById("idUsername");
}


getUsuario => {
    fetch('http://kg-azevedo.ml/api/tictactoe/accounts/b194f312-3f2d-11ed-a4a5-ac1f6b8b5c42')
        .then(function (response) {
            return response.json();
        })
        .then(function (data) {
            console.log(data.username);
        });
}
