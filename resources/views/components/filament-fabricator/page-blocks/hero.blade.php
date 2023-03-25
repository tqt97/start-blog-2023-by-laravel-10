@aware(['page', 'title', 'description', 'button', 'image'])
<div class="px-4 py-4 md:py-8">
    <div class="max-w-7xl mx-auto">
        <section class="text-gray-600 body-font">
            <div class="container mx-auto flex px-5 py-24 md:flex-row flex-col items-center">
                <div
                    class="lg:flex-grow md:w-1/2 lg:pr-24 md:pr-16 flex flex-col md:items-start md:text-left mb-16 md:mb-0 items-center text-center">
                    <h1 class="title-font sm:text-4xl text-3xl mb-4 font-medium text-gray-900">
                        {{ $title }}
                    </h1>
                    <p class="mb-8 leading-relaxed">{{ $description }}</p>
                    <div class="flex justify-center">

                        @foreach ($buttons as $key => $button)
                            <button
                                class="{{ $key > 0 ? 'ml-4' : '' }} inline-flex text-white bg-indigo-500 border-0 py-2 px-6 focus:outline-none hover:bg-indigo-600 rounded text-lg">
                                {{ $key }} {{ $button['button_text'] }}
                            </button>
                        @endforeach

                    </div>
                </div>
                <div class="lg:max-w-lg lg:w-full md:w-1/2 w-5/6">
                    {{-- <img class="object-cover object-center rounded" alt="hero" src="{{ $image }}"> --}}
                    {{-- <x-curator-curation class="object-cover object-center rounded" :media="{{ $image }}" curation="thumbnail" loading="lazy" /> --}}
                    <x-curator-glider class="object-cover w-auto" :media="$image" glide="" :srcset="['720w', '600w']"
                        sizes="(max-width: 1200px) 100vw, 1024px" />
                </div>
            </div>
        </section>
    </div>
</div>
