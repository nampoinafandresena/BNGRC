let actualiserBtn = document.getElementById('actualiserbtn');
actualiserBtn.addEventListener('click', function() {
    fetch("/recap/data")
        .then(response => response.json())
        .then(data => {

            document.getElementById("totalAmount").innerText =
                formatMoney(data.total);

            document.getElementById("distributedAmount").innerText =
                formatMoney(data.satisfied);

            document.getElementById("remainingAmount").innerText =
                formatMoney(data.reste);

        });
});

function formatMoney(value) {
    return new Intl.NumberFormat('fr-FR').format(value) + " Ar";
}