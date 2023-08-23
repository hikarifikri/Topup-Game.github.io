let list = document.querySelectorAll('.list');
let card = document.querySelectorAll('.card');

for(let i = 0; i<list.length; i++){
    list[i].addEventListener('click',function() {
        for (let j = 0; j < list.length; j++) {
            list[j].classList.remove('active');
        }
        this.classList.add('active');

        let dataFilter = this.getAttribute('data-filter');

        for(let k = 0; k < card.length; k++){
            card[k].classList.remove('active');
            card[k].classList.add('hide');

            if(card[k].getAttribute('data-item') == dataFilter || dataFilter == 'alt'){
                card[k].classList.remove('hide');
                card[k].add('active')
            }
        }
    })
}

