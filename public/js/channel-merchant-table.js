function generateMerchantHtml(index, pageNum, arrayIndex, arrayValue, numberPerPage, disabled){
    var numberPerPage = numberPerPage || 10;
    var disabled = disabled || false;
    var output = '';
    if(disabled){
        var disabledAttr = 'readonly disabled';
    }else{
        var disabledAttr = '';
    }
    if(index % (numberPerPage/2) == 0 && index != 0){
        output += '\
        </div>\
        <div class="col-xs-6">';
    }
    if(index % numberPerPage == 0 && index != 0){
        pageNum++;
        output += '\
            </div>\
        </div>\
        <div class="js-page-'+pageNum+' js-page">\
            <div class="col-xs-6">';
    }
    output += '\
    <div class="form-group has-feedback">\
        <div class="col-xs-7">\
            '+arrayValue+'\
        </div>\
        <div class="col-xs-5">\
            <div class="onoffswitch">\
                <input type="checkbox" name="merchant_id[]" value="'+arrayIndex+'" class="onoffswitch-checkbox" id="js-merchant-id-'+arrayIndex+'" '+disabledAttr+'>\
                <label class="onoffswitch-label" for="js-merchant-id-'+arrayIndex+'">\
                    <span class="onoffswitch-inner"></span>\
                    <span class="onoffswitch-switch"></span>\
                </label>\
            </div>\
        </div>\
    </div>';
    var response = [];
    response['output'] = output;
    response['pageNum'] = pageNum;
    return response;
}

function searchIdInMerchants(idValue, myArray){
    var arrayLength = myArray.length;
    for (var i=0; i < arrayLength; i++) {
        if(myArray[i] != "" && myArray[i] != undefined){
            if (myArray[i].id === idValue) {
                return myArray[i];
            }
        }
    }
}

function drawMerchantList(merchantsList, channel_merchants, searchFilter, sortName, sortChecked, numberPerPage, disabled){
    var  merchantsList      = merchantsList || [];
    var  channel_merchants  = channel_merchants || [];
    var  searchFilter       = searchFilter || '';
    var  sortName           = sortName || true;
    var  sortChecked        = sortChecked || true;
    var  numberPerPage      = numberPerPage || 10;
    var  disabled           = disabled || false;

    var pageNum = 1;
    var output = '<div class="js-page-'+pageNum+' js-page js-page-active">\
                    <div class="col-xs-6">';
    var actualIndex = 0;
    var response;
    var channelMerchants = [];

    $.each(channel_merchants, function( index, value ) {
        if(value != "" && value != undefined){
            var merchant = searchIdInMerchants(value, merchantsList);
            channelMerchants[index] = merchant;
        }
    });
    if(sortName == true){
        channelMerchants.sort(function(a, b){
            var nameA=a.name.toLowerCase(), nameB=b.name.toLowerCase();
            if (nameA < nameB) //sort string ascending
                return -1
            if (nameA > nameB)
                return 1
            return 0 //default return value (no sorting)
        });
        merchantsList.sort(function(a, b){
            var nameA=a.name.toLowerCase(), nameB=b.name.toLowerCase();
            if (nameA < nameB) //sort string ascending
                return -1
            if (nameA > nameB)
                return 1
            return 0 //default return value (no sorting)
        });
    }else{
        channelMerchants.sort(function(a, b){
            var nameA=a.id, nameB=b.id;
            if (nameA < nameB) //sort string ascending
                return -1
            if (nameA > nameB)
                return 1
            return 0 //default return value (no sorting)
        });
        merchantsList.sort(function(a, b){
            var nameA=a.id, nameB=b.id;
            if (nameA < nameB) //sort string ascending
                return -1
            if (nameA > nameB)
                return 1
            return 0 //default return value (no sorting)
        });
    }
    // To generate the checked merchant list
    if(sortChecked == true){
        $.each(channelMerchants, function( index, value ) {
            if(value != "" && value != undefined){
                if(value.name.toLowerCase().indexOf(searchFilter.toLowerCase()) >= 0){
                    response = generateMerchantHtml(actualIndex, pageNum, value.id, value.name, numberPerPage, disabled);
                    output += response['output'];
                    pageNum = response['pageNum'];
                    actualIndex++;   
                }
            }
        });
    }
    // Generate the rest of the list
    $.each(merchantsList, function( index, value ) {
        if(value != "" && value != undefined && value.name.toLowerCase().indexOf(searchFilter.toLowerCase()) >= 0){
            if(sortChecked == true){
                if($.inArray(value.id, channel_merchants) == -1){
                    response = generateMerchantHtml(actualIndex, pageNum, value.id, value.name, numberPerPage, disabled);
                    output += response['output'];
                    pageNum = response['pageNum'];
                    actualIndex++;
                }
            }else{
                response = generateMerchantHtml(actualIndex, pageNum, value.id, value.name, numberPerPage, disabled);
                output += response['output'];
                pageNum = response['pageNum'];
                actualIndex++;
            }
        }
    });
    output += '</div></div>';
    $('#merchants-list').html(output);

    // Set checked
    $.each(channel_merchants, function( index, value ) {
        $( "#js-merchant-id-"+value ).prop( "checked", true );        
    });

    // Initialize pagination tab
    if($('#js-pagination li').length > 0){
        $('#js-pagination').twbsPagination('destroy');
    }
    $('#js-pagination').twbsPagination({
        totalPages: pageNum,
        visiblePages: 4,
        onPageClick: function (event, page) {
            $('.js-page-active').removeClass('js-page-active');
            $('.js-page-'+page).addClass('js-page-active');
        }
    });

    $('body').trigger('complete.merchant');
}