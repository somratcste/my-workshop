<script>
    $.each($('form').serializeArray(),function (index,value) {
        $('[name="'+ value.name +'"]').attr('readonly','readonly');
    });
</script>