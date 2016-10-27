$(function(){
    // 获取配置列表
    getOptList();

    // semantic初始化
    $('#showopt').click(function(){
        getOptList();
        $('#optlist').sidebar('toggle');
    });
    $('.ui.dropdown').dropdown();
    $('.ui.checkbox').checkbox();

    // 添加参数
    $('#add_request').click(function () {
        addRequestRow();
    });
    $('#add_response').click(function () {
        addResponseRow();
    });
    $('#add_child').click(function () {
        addChildRow();
    });

    // 移除全部
    $('#removeall_request').click(function () {
        $('#request_arr').children(".fields:eq(0)").siblings().remove();
        addRequestRow();
    });
    $('#removeall_response').click(function () {
        $('#response_arr').children(".fields:eq(0)").siblings().remove();
        addResponseRow();
    });
    $('#removeall_child').click(function () {
        $('#child_arr').children(".fields:eq(0)").siblings().remove();
        addChildRow();
    });

    // 生成
    $('#create').click(function () {
        var isJson = $("#isjson").checkbox('is checked') ? 1 : 0;
        var isDoc = $("#isdoc").checkbox('is checked') ? 1 : 0;
        var json = '';
        if (isJson) {
            json = prompt('请输入JSON字符串，为避免中文被编码，请在json_encode的第二参数为JSON_UNESCAPED_UNICODE');
            if (json) {
                create(getAllInfo(), isJson, json, isDoc);
            } else if ('' === json) {
                alert('请输入JSON字符串');
            }
        } else {
            create(getAllInfo(), 0, '', isDoc);
        }
    });

});

function create(info, isJson, json, isDoc) {
    $('#result').empty().attr('rows', 2).html('NULL').closest('.segment').addClass('loading');
    $("#create").addClass('loading');
    $.ajax({
        url: './action.php?action=create',
        dataType: 'json',
        type: 'post',
        data: {info: info, isjson: isJson, json: json, isdoc: isDoc},
        success: function (response) {
            if (response.error) {
                alert(response.msg);
            } else {
                $('#result').empty().attr('rows', response.data.split("\n").length).html(response.data);
            }
            $('#create').removeClass('loading');
            $('#result').closest('.segment').removeClass('loading');
        },
        error: function () {
            alert('服务器错误');
            $('#create').removeClass('loading');
            $('#result').closest('.segment').removeClass('loading');
        }
    });
}

// 获取配置列表
function getOptList() {
    $.ajax({
        url: './action.php?action=getOptList',
        dataType: 'json',
        type: 'get',
        data:{},
        success: function(resopnse) {
            var html = '<div class="item"><a><i class="icon list layout"></i>配置列表</a></div>';
            for (var i = 0; i < resopnse.length; i++) {
                html += '<a class="item" data-content="' + resopnse[i] + '" onclick="loadAllInfo(this)">' + resopnse[i] + '</a>';
            }
            $('#optlist').html(html);
        }
    });
}

// 添加请求参数
function addRequestRow(request) {
    var obj = typeof (request) == 'object' ? request : [{name: '', required: 1, type: 'string', desc: ''}];
    var strHtml = '';
    for (var item = 0; item < obj.length; item++) {
        strHtml += '<div class="fields">' +
            '<div class="inline field" data-tooltip="删除">' +
            '<i class="circular icon link remove red" onclick="removeOneRow(this)"></i>' +
            '</div>' +
            '<div class="four wide field">' +
            '<input type="text" placeholder="参数名" value="' +
            obj[item]['name'] +
            '">' +
            '</div>' +
            '<div class="three wide field">' +
            '<select class="ui fluid dropdown">';
        strHtml += '<option value="0" ' + (obj[item]['required'] == 0 ? ' selected' :'') +'>否</option>'
            + '<option value="1" ' + (obj[item]['required'] == 1 ? ' selected' :'') +'>是</option>';
        strHtml += '</select>' +
            '</div>' +
            '<div class="three wide field">' +
            '<select class="ui fluid dropdown"> ';
        strHtml +=
            '<option value="string"' + (obj[item]['type'] == 'string' ? ' selected' :'') +  '>string</option>'
            + '<option value="integer"' + (obj[item]['type'] == 'integer' ? ' selected' :'') + '>integer</option>'
            + '<option value="json"' + (obj[item]['type'] == 'json' ? ' selected' :'') + '>json</option>'
            + '<option value="array"' + (obj[item]['type'] == 'array' ? ' selected' :'') + '>array</option>';
        strHtml += '</select>' +
            '</div>' +
            '<div class="six wide field">' +
            '<input type="text" placeholder="描述" value="' +
            obj[item]['desc'] +
            '">' +
            '</div>' +
            '</div>';
    }
    $('#request_arr').append(strHtml);
    $('.ui.dropdown').dropdown();
}

// 添加应答参数
function addResponseRow(response) {
    var obj = typeof (response) == 'object' ? response : [{name: '', desc: '', child: ''}];
    var strHtml = '';
    for (var item = 0; item < obj.length; item++) {
        strHtml += '<div class="inline fields">' +
            '<div class="field" data-tooltip="删除">' +
            '<i class="circular icon link remove red" onclick="removeOneRow(this)"></i>' +
            '</div>' +
            '<div class="six wide field">' +
            '<div class="ui icon input">' +
            '<i class="circular plus link icon" title="添加子参数" onclick="addChild(this)" data-content=' +
            "'" + (typeof (obj[item]['child']) == 'object' ? JSON.stringify(obj[item]['child']) : '') + "'" +
            '></i>' +
            '<input class="text" placeholder="参数" value="' +
            obj[item]['name'] +
            '">' +
            '</div>' +
            '</div>' +
            '<div class="ten wide field">' +
            '<input class="text" placeholder="描述" value="' +
            obj[item]['desc'] +
            '">' +
            '</div>' +
            '</div>';
    }
    $('#response_arr').append(strHtml);
}

// 初始化response
function initResponseRows(response) {
    if (typeof(response.result.child) == 'object') {
        addResponseRow(response.result.child);
    }
}

// 添加应答参数的子参数
var currentChild;
function addChild(e) {
    currentChild = e;
    var ele = $(e);
    var name = ele.next().val();
    if ('' !== name) {
        var content = ele.attr('data-content');
        $('#removeall_child').trigger('click');
        if ('' !== content) {
            addChildRow(JSON.parse(content));
        }
        var childEle = $('#addChild');
        childEle.find('.header').html('添加子参数[' + name + ']');
        childEle.modal({
            closable: false,
            observeChanges:true,
            onHidden: function () {
                currentChild = null;
            },
            onApprove: function () {
                saveChildArr();
                return true;
            }
        }).modal('show');
    }
}

// 添加子参数
function addChildRow(child) {
    var obj = typeof (child) == 'object' ? child : [{name: '', desc: ''}];
    var strHtml = '';
    for (var item = 0; item < obj.length; item++) {
        strHtml += '<div class="inline fields"> ' +
            '<div class="field" data-tooltip="删除">' +
            '<i class="circular icon link remove red" onclick="removeOneRow(this)"></i>' +
            '</div>' +
            '<div class="six wide field">' +
            '<input class="text" placeholder="参数" value="' +
            obj[item]['name'] +
            '"></div>' +
            '<div class="ten wide field">' +
            '<input class="text" placeholder="描述" value="' +
            obj[item]['desc'] +
            '"> ' +
            '</div>' +
            '</div>';
    }
    $('#child_arr').append(strHtml);
}

// 删除参数行
function removeOneRow(e) {
    e.closest('.fields').remove();
}

// 保存子参数信息
function saveChildArr() {
    var childEleArr = $('#child_arr').find('.fields');
    var arrContent = [];
    for (var i = 0; i < childEleArr.length; i++) {
        if (i > 0) {
            var objContent = {};
            var info = $(childEleArr[i]).find('input');console.log(childEleArr[i]);
            var name = $(info[0]).val();
            if (name) {
                objContent.name = name;
                objContent.desc = $(info[1]).val();
                arrContent.push(objContent);
            }
        }
    }
    var strContent = JSON.stringify(arrContent);console.log(arrContent);
    strContent = '[]' == strContent ? '' : strContent;
    $(currentChild).attr('data-content', strContent);
    currentChild = null;
}

// 加载配置
function loadAllInfo(e) {
    var filename = e.getAttribute('data-content');
    $('#param_form').addClass('loading');
    $('#result').empty().html('NULL').attr('rows', 2);
    $.ajax({
        url: './action.php?action=getOptInfo',
        dataType: 'json',
        type: 'get',
        data: {filename: filename},
        success: function (response) {
            if (response.error) {
                alert(response.msg);
            } else {
                $('#removeall_request').trigger('click');
                $('#removeall_response').trigger('click');
                var info = response.data;
                $('#title').val(info.title);
                $('#url').val(info.url);
                $('#desc').val(info.desc);
                addRequestRow(info.request);
                initResponseRows(info.response);
            }
            $('#param_form').removeClass('loading');
        },
        error: function () {
            $('#param_form').removeClass('loading');
            alert('服务器错误');
        }
    });
}

// 保存配置
function saveAllinfo() {
    var info = getAllInfo();
    var title = '';
    if ((title = prompt('请输入配置名称'))) {
        $.ajax({
            url: './action.php?action=savetojson',
            dataType: 'json',
            data: {title: title, info: info},
            type: 'post',
            success: function (response) {
                alert(response.msg);
                getOptList();
            },
            error: function () {
                alert('服务器错误');
            }
        });
    } else if ('' === title) {
        alert('配置名不为空');
    }
}

// 获取所有信息
function getAllInfo() {
    var info = {};
    info.title = $('#title').val();
    info.url = $('#url').val();
    info.desc = $('#desc').val();
    info.request = getRequestInfo();
    info.response = getResponseInfo();
    return info;
}

// 获取请求参数
function getRequestInfo() {
    var request = [];
    var requestEle = $('#request_arr').find('.fields');
    for (var i = 0; i < requestEle.length; i++) {
        if (i > 0) {
            var temp = {};
            var inputEle = $(requestEle[i]).find('input');
            var selectEle = $(requestEle[i]).find('select');
            if ('' !== inputEle[0].value) {
                temp.name = inputEle[0].value;
                temp.desc = inputEle[1].value;
                temp.required = $(selectEle[0]).find('option:selected').val();
                temp.type = $(selectEle[1]).find('option:selected').val();
                request.push(temp);
            }
        }
    }
    return request;
}

// 获取应答消息
function getResponseInfo() {
    var response = {};

    var getErrs = function (name) {
        var objName = {};
        var errnoEle = $('#response_' + name);
        var errnoArr = errnoEle.find('input');
        objName.name = errnoArr[0].value;
        objName.desc = errnoArr[1].value;
        objName.type = errnoEle.find('select').find('option:selected').val();
        return objName;
    };

    response.errno = getErrs('errno');
    response.errmsg = getErrs('errmsg');
    response.result = getErrs('result');

    var ele = $('#response_arr').find('.fields');
    var child = [];
    for (var i = 0; i < ele.length; i++) {
        if (i > 0) {
            var arrData = $(ele[i]).find('input');
            if ('' !== arrData[0].value) {
                var tempObj = {};
                var content = $(arrData[0].closest('.input')).find('.plus').attr('data-content');
                if ('' !== content) {
                    tempObj.child = JSON.parse(content);
                }
                tempObj.name = arrData[0].value;
                tempObj.desc = arrData[1].value;
                child.push(tempObj);
            }
        }
    }
    response.result.child = child;
    return response;
}
