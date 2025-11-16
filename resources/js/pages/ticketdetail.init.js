$('.basicAutoSelect').autoComplete({
    events: {
        searchPost: function (resultFromServer) {
            let arr = [];
            for (let i = 0; i < resultFromServer.length; i++) {
                let element = resultFromServer[i];
                arr.push({ "value": element.id, "text": element.prenoms+' '+element.nom});
            }
            return arr;
        }
    }
});

// favourite btn
document.querySelectorAll(".favourite-btn").forEach(function (item) {
    item.addEventListener("click", function (event) {
        this.classList.toggle("active");
    });
});