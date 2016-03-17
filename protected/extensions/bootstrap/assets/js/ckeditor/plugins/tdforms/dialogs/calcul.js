(function($, undefined) {
    $(document).ready(function() {

        function calValue() {

            $("[id^='calculation_data_']").each(function() {
                var node = $(this).attr("data-node");
                var calculation = $(this).val();
                var result = 0;
                var arrId = [];
                $('.calcul[name="' + node + '"]').each(function() {
                    if ($(this).attr('data-node')) {
                        arrId.push($(this).attr('data-node'));
                    }
                });
                if (arrId.length <= 0) {
                    $('.cal_result[id="' + node + '"]').val(calculation);
                    return;
                } else {
                    for (var i = 0; i < arrId.length; i++) {
                        var inputId = $('#data_' + arrId[i]).length>0 ? '#data_' + arrId[i] : "#" + node + "_"+ arrId[i];
                        calculation = calculation.replace(arrId[i], 'getVal("' + inputId + '")');
                    }
                }
                result = eval(calculation);
                $('.cal_result[id="' + node + '"]').val(result);
            });
            setTimeout(calValue, 500);
        }

        function getVal(valueId) {
            if ($(valueId).val() === '') {
                $(valueId).val(0);
            }
            return parseFloat($(valueId).val());
        }

        setTimeout(calValue, 500);

        //---- 计算控件相关函数 ----
        function RMB(currencyDigits)
        {
            // Constants:
            var MAXIMUM_NUMBER = 99999999999.99;
            // Predefine the radix characters and currency symbols for output:
            var CN_ZERO = "零";
            var CN_ONE = "壹";
            var CN_TWO = "贰";
            var CN_THREE = "叁";
            var CN_FOUR = "肆";
            var CN_FIVE = "伍";
            var CN_SIX = "陆";
            var CN_SEVEN = "柒";
            var CN_EIGHT = "捌";
            var CN_NINE = "玖";
            var CN_TEN = "拾";
            var CN_HUNDRED = "佰";
            var CN_THOUSAND = "仟";
            var CN_TEN_THOUSAND = "万";
            var CN_HUNDRED_MILLION = "亿";
            var CN_DOLLAR = "元";
            var CN_TEN_CENT = "角";
            var CN_CENT = "分";
            var CN_INTEGER = "整";

            // Variables:
            var integral; // Represent integral part of digit number.
            var decimal; // Represent decimal part of digit number.
            var outputCharacters; // The output result.
            var parts;
            var digits, radices, bigRadices, decimals;
            var zeroCount;
            var i, p, d;
            var quotient, modulus;

            // Validate input string:
            currencyDigits = currencyDigits.toString();
            if (currencyDigits == "") {
                return "";
            }
            if (currencyDigits.match(/[^,.\d]/) != null) {
                return "";
            }
            if ((currencyDigits).match(/^((\d{1,3}(,\d{3})*(.((\d{3},)*\d{1,3}))?)|(\d+(.\d+)?))$/) == null) {
                return "";
            }

            // Normalize the format of input digits:
            currencyDigits = currencyDigits.replace(/,/g, ""); // Remove comma delimiters.
            currencyDigits = currencyDigits.replace(/^0+/, ""); // Trim zeros at the beginning.
            // Assert the number is not greater than the maximum number.
            if (Number(currencyDigits) > MAXIMUM_NUMBER) {
                return "";
            }

            // Process the coversion from currency digits to characters:
            // Separate integral and decimal parts before processing coversion:
            parts = currencyDigits.split(".");
            if (parts.length > 1) {
                integral = parts[0];
                decimal = parts[1];
                // Cut down redundant decimal digits that are after the second.
                decimal = decimal.substr(0, 2);
            }
            else {
                integral = parts[0];
                decimal = "";
            }
            // Prepare the characters corresponding to the digits:
            digits = new Array(CN_ZERO, CN_ONE, CN_TWO, CN_THREE, CN_FOUR, CN_FIVE, CN_SIX, CN_SEVEN, CN_EIGHT, CN_NINE);
            radices = new Array("", CN_TEN, CN_HUNDRED, CN_THOUSAND);
            bigRadices = new Array("", CN_TEN_THOUSAND, CN_HUNDRED_MILLION);
            decimals = new Array(CN_TEN_CENT, CN_CENT);
            // Start processing:
            outputCharacters = "";
            // Process integral part if it is larger than 0:
            if (Number(integral) > 0) {
                zeroCount = 0;
                for (i = 0; i < integral.length; i++) {
                    p = integral.length - i - 1;
                    d = integral.substr(i, 1);
                    quotient = p / 4;
                    modulus = p % 4;
                    if (d == "0") {
                        zeroCount++;
                    }
                    else
                    {
                        if (zeroCount > 0)
                        {
                            outputCharacters += digits[0];
                        }
                        zeroCount = 0;
                        outputCharacters += digits[Number(d)] + radices[modulus];
                    }
                    if (modulus == 0 && zeroCount < 4) {
                        outputCharacters += bigRadices[quotient];
                    }
                }
                outputCharacters += CN_DOLLAR;
            }
            // Process decimal part if there is:
            if (decimal != "") {
                for (i = 0; i < decimal.length; i++) {
                    d = decimal.substr(i, 1);
                    if (d != "0") {
                        outputCharacters += digits[Number(d)] + decimals[i];
                    }
                }
            }
            // Confirm and return the final output string:
            if (outputCharacters == "") {
                outputCharacters = CN_ZERO + CN_DOLLAR;
            }
            if (decimal == "") {
                outputCharacters += CN_INTEGER;
            }
            //outputCharacters = CN_SYMBOL + outputCharacters;
            return outputCharacters;
        }

        function MAX()
        {
            if (arguments.length == 0)
                return;
            var max_num = arguments[0];
            for (var i = 0; i < arguments.length; i++)
                max_num = Math.max(max_num, arguments[i]);
            return parseFloat(max_num);
        }

        function MIN()
        {
            if (arguments.length == 0)
                return;
            var min_num = arguments[0];
            for (var i = 0; i < arguments.length; i++)
                min_num = Math.min(min_num, arguments[i]);
            return parseFloat(min_num);
        }

        function MOD()
        {
            if (arguments.length == 0)
                return;
            var first_num = arguments[0];
            var second_num = arguments[1];
            var result = first_num % second_num;
            result = isNaN(result) ? "" : parseFloat(result);
            return result;
        }

        function ABS(val)
        {
            return Math.abs(parseFloat(val));
        }

        function AVG()
        {
            if (arguments.length == 0)
                return;
            var sum = 0;
            for (var i = 0; i < arguments.length; i++)
            {
                sum += parseFloat(arguments[i]);
            }
            return parseFloat(sum / arguments.length);
        }


        function DAY(val)
        {
            return val == 0 ? 0 : Math.floor(val / 86400);
        }

        function HOUR(val)
        {
            return val == 0 ? 0 : Math.floor(val / 3600);
        }

        function DATE(val)
        {
            return (val >= 0) ? Math.floor(val / 86400) + '天' + Math.floor((val % 86400) / 3600) + '小时' + Math.floor((val % 3600) / 60) + '分' + Math.floor(val % 60) + '秒' : '日期格式无效';//'日期格式无效'
        }

        function GETVAL(val)
        {
            var obj = document.getElementsByName(val);

            if (obj.length == 0)
                return 0;

            if (obj[0].className == 'LIST_VIEW')
            {
                return document.getElementById("LV_" + val.substring(5));
            }

            var vVal = obj[0].value;
            //判断是否为时间
            if (vVal.indexOf("-") > 0)
            {

                //eval("date_flag_"+flag+"=1");
                vVal = vVal.replace(/\-/g, "/");
                var d = new Date(vVal);
                return d.getTime() / 1000;
            } else if (vVal.indexOf("%") > 0) { //处理%
                vVal = parseFloat(vVal) / 100;
            } else if (vVal.indexOf(" ") >= 0) {
                obj[0].value = obj[0].value.replace(/\s+/g, "");
                vVal = obj[0].value;
            } else if (is_ths(vVal)) {
                vVal = calc_ths_rev(vVal);
            } else if (vVal == "" || isNaN(vVal)) {
                vVal = 0;
            }
            return parseFloat(vVal);
        }

        function LIST(olist, col)
        {
            if (!olist)
                return 0;
            if (!col)
                return 0;

            //col--;  
            var output = 0;
            var lv_tb_id = olist.getAttribute("id");
            var row_length = olist.rows.length;
            if (document.getElementById(lv_tb_id + '_sum'))
                row_length--;

            for (i = 1; i < row_length; i++)
            {
                for (j = 0; j < olist.rows[i].cells.length - 1; j++)
                {
                    if (j == col)
                    {
                        var child_ojb = olist.rows[i].cells[j].firstChild;
                        var olist_val = olist.rows[i].cells[j].firstChild.value;
                        olist_val = (typeof olist_val == "undefined" || olist_val == "" || olist_val.replace(/\s/g, '') == "") ? 0 : olist_val;
                        olist_val = (isNaN(olist_val)) ? NaN : olist_val;
                        if (child_ojb && child_ojb.tagName)
                            output += parseFloat(olist_val);
//                if(child_ojb && child_ojb.tagName)
//                    output += parseFloat(olist.rows[i].cells[j].firstChild.value);
                        else
                            output += parseFloat(olist.rows[i].cells[j].innerText);
                    }
                }
            }
            return  parseFloat(output);
        }
    })
})(jQuery)