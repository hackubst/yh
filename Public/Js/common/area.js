var province_id = $('#pre_province_id').val();
var city_id = $('#pre_city_id').val();
var area_id = $('#pre_area_id').val();
$(function()
{
	if (province_id) {
		//显示城市列表
		change_city(province_id, city_id);
	}
	if (city_id) {
		//显示地区列表
		change_area(city_id, area_id);
	}

	$('#province_id').change(function() {
		province_id = $(this).val();
		change_city(province_id);
		change_address(1);
	});

	$('#city_id').change(function() {
		city_id = $(this).val();
		change_area(city_id);
		change_address(2);
	});

	$('#area_id').change(function() {
		area_id = $(this).val();
		change_address(3);
	});
});

function change_city(province_id, city_id) {
    $('#area_id').html("<option value='0'>--选择地区--</option>");
    $.post('/Area/get_city_list', {
        "province_id": province_id
    }, function(data) {
        if (!data) {
            return false;
        }
        var result = eval(data);
        var length = result.length;
        var str = '<option value="0">--选择城市--</option>';
        for (i = 0; i < length; i++) {
            str += '<option value="' + result[i]['city_id'] + '">' + result[i]['city_name'] + '</option>';
        }
        $('#city_id').html(str);
        if (city_id) {
            $('#city_id option[value=' + city_id + ']').attr('selected', 'selected');
        }
    }, 'json');
	if (typeof(eval("calculateExpressFee")) == "function")
	{
		calculateExpressFee();
		$('#error_area').html('');
	}
}

function change_area(city_id, area_id) {
    $.post('/Area/get_area_list', {
        "city_id": city_id
    }, function(data) {
        if (data == null) {
            $('#area_id').html('<option value="0">--选择地区--</option>');
            $('#div_area').css('display', 'none');
            return;
        } else {
            $('#div_area').css('display', '');
        }

        var result = eval(data);
        var length = result.length;
        var str = '<option value="0">--选择地区--</option>';
        for (i = 0; i < length; i++) {
            str += '<option value="' + result[i]['area_id'] + '">' + result[i]['area_name'] + '</option>';
        }

        $('#area_id').html(str);
        if (area_id) {
            $('#area_id option[value=' + area_id + ']').attr('selected', 'selected');
        }
    }, 'json');
}

function change_address(level)
{
	var address = '';
	if (province_id > 0)
	{
		address += $('#province_id').find('option:selected').text() + ' ';
	}
	if (city_id > 0 && level > 1)
	{
		address += $('#city_id').find('option:selected').text() + ' ';
	}
	if (area_id > 0 && level > 2)
	{
		address += $('#area_id').find('option:selected').text() + ' ';
	}
	$('#address').val(address); //更改地址前缀
}

