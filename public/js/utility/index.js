/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
(function($) {
    "user strict";


})(jQuery);
$(document).on('click', '.btn-multi', function(event) {

    bootbox.confirm({
        title: "Confirm",
        message: "Are you sure you want to delete records? This process cannot be undone.",
        buttons: {
            cancel: {
                label: 'Cancel',
                className: 'btn-gray'
            },
            confirm: {
                label: 'Delete',
                className: 'btn-red'
            }
        },
        callback: function(result) {
            //Code Here            
        }
    });
});
$(document).on('click', '.btn-single', function(event) {
    bootbox.alert({
        title: "Alert",
        message: "Please select atleast one record to delete.",
        size: 'small'
    });
});

$('.custom-select-search').selectpicker({
    liveSearch:true,
    size:10
});

var citynames = new Bloodhound({
  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
  queryTokenizer: Bloodhound.tokenizers.whitespace,
  prefetch: {
    url: 'https://bootstrap-tagsinput.github.io/bootstrap-tagsinput/examples/assets/citynames.json',
    filter: function(list) {
      return $.map(list, function(cityname) {
        return { name: cityname }; });
    }
  }
});
citynames.initialize();

$('#tag1').tagsinput({
  typeaheadjs: {
    name: 'citynames',
    displayKey: 'name',
    valueKey: 'name',
    source: citynames.ttAdapter()
  }
});