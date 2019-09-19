/**
 * Created by Yan on 2019/9/11.
 */

/**
 * 封装ajax请求方法
 * @param url
 * @param type
 * @param data
 */
function ajaxRequest(url, type, data){
    $.ajax({
        url: url ? url : '', // 设置参数 url 的默认值为 '' ,也可以写作：url: url || ''
        type: type ? type : 'post',
        data: data ? data : '',

        // 请求发送前的回调函数，用来修改请求发送前jqXHR（在 jQuery 1.4.x 中的 XMLHttpRequest）对象，此功能用来设置自定义 HTTP 头信息等。
        beforeSend: function (jqXHR, settings) {
            this.layerIndex = layer.msg('请求中…', {
                icon: 16,
                shade: 0.01
            }); //layer.load(0, { shade: [0.5, '#393D49'] });

            // 禁用按钮防止重复提交
            //$("button").attr({ disabled: "disabled" });
        },

        // 请求成功后的回调函数。这个函数传递3个参数：从服务器返回的数据（并根据dataType参数进行处理后的数据），一个描述状态的字符串，还有 jqXHR（在jQuery 1.4.x前为XMLHttpRequest）对象。
        success: function (data, textStatus, jqXHR) {
            if(data.status != 1){ // 执行 error:function(){}，由于后台返回的HTTP状态码导致
                layer.msg(data.message, {icon: 5, time: 1000}); // 失败的表情
                return;
            }else if(data.status == 1){
                layer.msg(data.message, {
                    icon: 1, // 成功的表情
                    time: 1000 // 1秒关闭（如果不配置，默认是3秒）
                }, function(){
                    // （关闭弹窗）返回上级页面
                    if (data.data.url == 'parent') {
                        window.parent.location.reload();return;
                    }
                    window.location.replace(data.data.url);
                });
            }
        },

        // 请求失败时调用此函数（默认: 自动判断 xml 或 html）。3个参数：jqXHR (在 jQuery 1.4.x 前为 XMLHttpRequest) 对象，错误信息，（可能）捕获的异常对象。
        error: function (jqXHR, textStatus, errorThrown) {
            layer.msg(jqXHR.responseJSON.message, {icon: 5, time: 1000});

            /*// 刷新验证码：如果后台（在判断账号、密码前）先判断验证码，则需要刷新验证码；否则即不刷新的话，在第一次验证码通过后，但是当账号或密码出错时，后台验证码已改变
             $('#captcha_img').attr('src', '/index.php/captcha'); // jquery修改img的src值
             //console.log($('#captcha_img')[0].src); // jquery获取img的src值*/
        },

        // 请求完成后回调函数 (请求成功或失败时均调用)。2个参数：jqXHR（jQuery 1.4.x 中是 XMLHttpRequest）对象，一个描述请求状态的字符串("success", "notmodified", "error", "timeout", "abort", 或者 "parsererror") 。
        complete: function (jqXHR, textStatus) {
            layer.close(this.layerIndex);
        }
    });
}
