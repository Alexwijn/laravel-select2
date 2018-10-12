@section(config('select2.view'))
    <script>
        $('{{ $element }}').select2({!! $parameters->toJson() !!});
    </script>
@append