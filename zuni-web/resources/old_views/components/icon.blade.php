@props(['name'])

{!! file_get_contents(public_path("images/icons/{$name}.svg")) !!}