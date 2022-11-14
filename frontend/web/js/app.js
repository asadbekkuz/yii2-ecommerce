$(function (){
    const $cartButton = $('.btn-add-to-cart');
    const $productCount = $('#product-counter');
    $cartButton.click((e)=>{
        e.preventDefault();
        const $this = $(e.target);
        const id = $this.closest('.product-item').data('key');
        $.ajax({
            url:'/cart/add',
            method:'POST',
            data:{id},
            success:function (success){
                $productCount.text((index,curentcontent)=>Number(curentcontent) + 1);
            }
        })
    })
})