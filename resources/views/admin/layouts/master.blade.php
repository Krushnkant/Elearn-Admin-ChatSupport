@include('admin.layouts.head')
@include('admin.layouts.header')
@include('admin.layouts.sidebar')
	@if (session()->get('success'))
        @php
            $type = 'success';
        @endphp
        @if(is_array(json_decode(session()->get('success'), true)))
            @php
                $message = implode('', session()->get('success')->all(':message<br/>'));
            @endphp
        @else
            @php
                $message = session()->get('success');
            @endphp
        @endif
    @elseif (session()->get('warning'))
        @php
            $type = 'warning';
        @endphp
        @if(is_array(json_decode(session()->get('warning'), true)))
            @php
                $message = implode('', session()->get('warning')->all(':message<br/>'));
            @endphp
        @else
            @php
                $message = session()->get('warning');
            @endphp
        @endif
    @endif
    @yield('content')
  
@include('admin.layouts.footer')
      
