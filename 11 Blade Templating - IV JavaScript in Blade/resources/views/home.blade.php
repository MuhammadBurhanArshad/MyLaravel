@extends('layouts.masterlayout')

@section('content')
    <h1>Main Heading</h1>
    <h2>Home Page</h2>
    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Deleniti dolores, doloribus accusamus velit debitis accusantium quae tenetur aliquam at laborum ullam assumenda nulla blanditilis. Veniam delectus magni quisquam numquam id pariatur voluptates cumque minima nemo vitae eveniet sint nobis veritatis omnis unde, natus rem, praesentium debitis quial Delectus harum sapiente non mollitia veniam accusamus quas recusandae, repellendus tenetur illo dolorem dicta nostrum laborum nihil. Temporibus, quas non dolorem itaque, exercitationem magni praesentium omnis a odio impedit dolore? Voluptas commodi explicabo praesentium exercitationem culpa tenetur dolorem officiis? Impedit, accusamus explicabo repellendus labore odit, itaque suscipit incidunt dignissimos, dolorem autem fugiat aperiam?</p>

    {{-- verbatim is used to add JS dynamic values in the blade template, as here we are using VueJS message variable --}}
    @verbatim
        <div id="app">{{ message }}</div>
    @endverbatim

@endsection

@section('footer')
    @parent
    <a href="https://burhan.is-great.net" target="_blank">My Website</a>
@endsection

@section('title', 'Home Page')

{{-- we can also push the same same name stack multiple time unlike the normal section --}}
@push('scripts')
    <script>
        console.log('This is a script from home.blade.php');
    </script>
@endpush

@push('styles')
    <style>
        body {
            /* background-color: #f0f0f0; */
        }
    </style>
@endpush

{{-- prepand is used to define the location of the pushed content, that is will be inserted at the beginning of the stack --}}
@push('styles')
    <style>
        h1 {
            /* color: blue; */
        }
    </style>
@endpush

@push('scripts')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>

<script>
  const { createApp, ref } = Vue

  createApp({
    setup() {
      const message = ref('Hello vue!')
      return {
        message
      }
    }
  }).mount('#app')
</script>
@endpush