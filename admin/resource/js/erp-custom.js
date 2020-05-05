function select2FormatState (opt) {
    if (!opt.id) {
        return opt.text;
    }
    var optimage = $(opt.element).data('image');
    if(!optimage){
        return opt.text;
    } else {
        var $opt = $(
            '<span><img class="avatar avatar-30 img-rounded" src="' + optimage + '"/> ' + opt.text + '</span>'
        );
        return $opt;
    }
};

function getQueryVariable(variable) {
    const query = window.location.search.substring(1);
    const vars = query.split("&");
    for (let i=0;i<vars.length;i++) {
        let pair = vars[i].split("=");
        if(pair[0] == variable){return pair[1];}
    }
    return(false);
}

//alert
let alertTimeout = null;
function showAlert(message,type=null){
    let $alertItem = $("#adminAlert");
    let $textItem = $alertItem.find("span");
    let $closeBtn = $alertItem.find(".closed");
    clearTimeout(alertTimeout);
    if(type=="error"){
        $alertItem.attr("class","myadmin-alert alert-danger myadmin-alert-top alerttop");
    }else{
        $alertItem.attr("class","myadmin-alert alert-success myadmin-alert-top alerttop");
    }
    $textItem.text(message);
    $alertItem.stop().slideDown(function(){
        alertTimeout = setTimeout(function(){
            $alertItem.stop().slideUp();
        },8000)
    });
    $closeBtn.click(function(){
        $alertItem.stop().slideUp();
    });
}

function hideAlert(){
    $("#adminAlert").stop().slideUp();
}

function setDefaultValue(){
    $("select[data-defvalue]").each(function () {
        var val = $(this).attr("data-defvalue");
        var option = $(this).find("option");
        option.each(function(){
            if($(this).val()==val){
                $(this).prop("selected", "selected");
            }
        })
    });
}

$(document).ready(function () {

    //select Default value
    setDefaultValue();

    //上传插件
    if($('.dropify').length>0){
        $('.dropify').dropify();
    }

    //checkBox
    $("#cBoxAll").change(function () {
        if(this.checked){
            $(".cBox").each(function(){
                $(this).prop("checked",true);
                $("#deleteBtn").show();
            })
        }else{
            $(".cBox").each(function(){
                $(this).prop("checked",false)
                $("#deleteBtn").hide();
            })
        }
    });

    $(".cBox").each(function(){
        $(this).change(function(){
           if($(".cBox:checked").length>0){
               $("#deleteBtn").show();
           }else{
               $("#deleteBtn").hide();
           }
        })
    })

    //修改上传图片
    $('#currentImages').children('div').each(function(i,ele){
        let $btn = $(ele).find('.overlay');
        $btn.click(function(){
            $(ele).remove();
        });
    });


    //高亮搜索
    const searchValue = getQueryVariable('searchValue');
    $("*[data-hl-search]").each(function(){
        const htmlText = $(this).html();
        const exp = new RegExp(searchValue,"gi");
        const newText = htmlText.replace(exp,"<span class='text-danger'>$&</span>");
        $(this).html(newText);
    })

    //高亮排序
    const orderByValue = getQueryVariable('orderBy');
    $(`*[data-hl-orderby=${orderByValue}]`).each(function(){
        $(this).addClass('highlight')
    })

    $(".user-search-select-ajax").select2({
        ajax: {
            url: "/restAPI/userController.php?action=searchUser",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    searchValue: params.term
                };
            },
            processResults: function (data) {
                return {
                    results: $.map(data.result, function (item) {
                        return {
                            text: `${item.user_first_name} ${item.user_last_name} (${item.user_email})`,
                            id: item.user_id
                        }
                    })
                };
            },
            cache: true
        },
        placeholder: 'Search for a user',
        minimumInputLength: 1
    });


});
