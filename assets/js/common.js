const bk_medialibrary_main = {
    time_delay: 1000,
    end_point: null,
    _wpnonce: null,
    init:function(){
        bk_medialibrary_main.end_point = bbk_media_library.api_public;
        bk_medialibrary_main._wpnonce = bbk_media_library.nonce;
        bk_medialibrary_main.enabledBtnCalculate();
    },
    enabledBtnCalculate:function(){
        const buttons = document.querySelectorAll('.js-bkml-calculate-size');
        buttons.forEach(button => {
            button.addEventListener('click', bk_medialibrary_main.actionCalculate, false);
        });
    },
    disabledBtnCalculate:function(){
        const buttons = document.querySelectorAll('.js-bkml-calculate-size');
        buttons.forEach(button => {
            const value = button.getAttribute("data-id");
            button.removeEventListener('click', bk_medialibrary_main.actionCalculate, false);
        });
    },
    actionCalculate:function(e){
        const button = e.currentTarget;
        button.classList.toggle('send');
        const value = button.getAttribute("data-id");
        bk_medialibrary_main.calculate(value);
    },
    calculate:function(value){
        const url = `${bk_medialibrary_main.end_point}/calculate-file-size`;
        const media_id = value;
        const _wpnonce = bk_medialibrary_main._wpnonce;
        const data =  {media_id, _wpnonce};

        const settings = {
            method: 'POST',
            body: JSON.stringify(data),
            headers: {
                Accept: 'application/json',
                'Content-Type': 'application/json',
            }
        };

        fetch( url , settings)
            .then(response => response.json())
	        .then(result => {
                if ( result.success ) {
                    const btn = document.querySelector(`.js-bkml-calculate-size[data-id="${media_id}"]`);
                    const element = btn.parentNode;
                    element.innerHTML = result.data.filesize;

                    bk_medialibrary_main.disabledBtnCalculate();
                    setTimeout( bk_medialibrary_main.enabledBtnCalculate, bk_medialibrary_main.time_delay );
                }
            })
	        .catch(err => console.error(err));

    }

}

window.addEventListener("load", e => setTimeout( bk_medialibrary_main.init, 300 ) );