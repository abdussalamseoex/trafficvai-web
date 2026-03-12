<x-frontend-layout>
    @foreach($sections as $section)
        @if(view()->exists("home.sections.{$section->key}"))
            @include("home.sections.{$section->key}", ['section' => $section])
        @else
            <!-- Missing partial for section key: {{ $section->key }} -->
        @endif
    @endforeach
</x-frontend-layout>
