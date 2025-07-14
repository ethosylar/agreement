const inputs = document.querySelectorAll(".input");

function addcl(){
    let parent = this.parentNode.parentNode;
    parent.classList.add("focus");
}

function remcl(){
    let parent = this.parentNode.parentNode;
    if(this.value == ""){
        parent.classList.remove("focus");
    }
}

inputs.forEach(input => {
    input.addEventListener("focus", addcl);
    input.addEventListener("blur", remcl);
});

// Shared sidebar interactions
$(document).ready(function () {
    $('.toggle-btn').click(function () {
        $('.sidebar').toggleClass('active');
    });

    document.querySelectorAll('.list-item').forEach(item => {
        item.addEventListener('mouseenter', () => {
            document.querySelector('.sidebar').classList.add('active');
        });
        item.addEventListener('mouseleave', () => {
            document.querySelector('.sidebar').classList.remove('active');
        });
    });
});
