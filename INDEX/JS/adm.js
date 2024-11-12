document.getElementById('open_btn').addEventListener('click',function(){
    document.getElementById('sidebar').classList.toggle('open-sidebar');
});

function carregarPagina(url) {
    fetch(url)
        .then(response => {
            console.log(response); // Adicione esta linha para verificar a resposta
            return response.text();
        })
        .then(html => {
            document.getElementById('content').innerHTML = html;
        })
        .catch(error => console.error('Erro ao carregar a página:', error));
}


