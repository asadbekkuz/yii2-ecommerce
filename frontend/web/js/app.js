$(function (){
    const $cartButton = $('.btn-add-to-cart');
    const $productCount = $('#product-counter');
    const $cartQuantity = $('.cart-item-quantity');
    $cartButton.click((e)=>{
        e.preventDefault();
        const $this = $(e.target);
        const id = $this.closest('.product-item').data('key');
        const url = $this.closest('.product-item').data('url');
        $.ajax({
            url: url,
            method:'POST',
            data:{id},
            success:function (success){
                $productCount.text((index,curentcontent)=>Number(curentcontent) + 1);
            }
        })
    })
    $cartQuantity.change((ev)=>{
        const $this = $(ev.target);
        const id = $this.closest('.row-item-quantity').data('key');
        const url = $this.closest('.row-item-quantity').data('url');
        let value = $this.val();
        $.ajax({
            url:url,
            method: 'POST',
            data:{id,value},
            success:function (data){
                if(data.success){
                    $productCount.text(data.quantity);
                }
            }
        })
    })
})