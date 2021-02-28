@extends('api::page')

@section('content')
    <h2 class="title text-center mb-3">FAQ</h2>
    <div class="accordion accordion-flush" id="faq-accordion">
        @foreach($faqs as $faq)
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#item-{{$loop->index}}">
                        {{$faq['question']}}
                    </button>
                </h2>
                <div id="item-{{$loop->index}}" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                    <div class="accordion-body">
                        {!!$faq['answer']!!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
