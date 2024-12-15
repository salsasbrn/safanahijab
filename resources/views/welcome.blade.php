<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hijab Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@2.51.5/dist/full.css" rel="stylesheet" type="text/css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&family=Host+Grotesk:ital,wght@0,300..800;1,300..800&family=Iceland&family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Parkinsans:wght@300..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');

        .crimson {
            font-family: "Crimson Text";
        }

        .grotesk {
            font-family: "Host Grotesk"
        }
    </style>
</head>

<body class="bg-white text-neutral">
    <!-- Header -->
    <header class="bg-light text-neutral h-screen">
        <div class="navbar bg-white text-neutral">
            <div class="navbar-start">
                <div class="dropdown">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                    <ul tabindex="0"
                        class="menu menu-sm dropdown-content bg-white rounded-box z-[1] mt-3 w-52 p-2 shadow">
                        <li><a>Homepage</a></li>
                        <li><a>Portfolio</a></li>
                        <li><a>About</a></li>
                    </ul>
                </div>
            </div>
            <div class="navbar-center">
                <a class="btn btn-ghost text-xl">Safana</a>
            </div>
            <div class="navbar-end">
                <button class="btn btn-ghost btn-circle">
                    <div class="indicator">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="badge badge-xs badge-primary indicator-item"></span>
                    </div>
                </button>
                <button class="btn btn-ghost btn-circle">
                    <a href="{{ route('login') }}">
                        <img width="20" height="20" src="https://img.icons8.com/ios-glyphs/30/user--v1.png"
                            alt="user--v1" />
                    </a>
                </button>
            </div>
        </div>
        <div class="w-full relative">
            <img src={{ asset('images/Banner.png') }} alt="">
            <button class="btn btn-base-content absolute top-96 left-96 ml-5 mt-5"><a href="{{ route('login') }}">
                    Shop Now
                </a></button>
        </div>
    </header>
    <div class="w-full h-screen flex justify-center items-center flex-col crimson my-10">
        <h1 class="text-3xl font-semibold mb-2">Our best sellers</h1>
        <p>The most popular products! Choose from our best-selling collection and experience the difference.</p>
        <div class="flex gap-2 font-semibold text-center mt-4 grotesk">
            @foreach ($bestSellers as $product)
                <div class="">
                    <img src="{{ route('product.image', $product->id) }}" alt="{{ $product->name }}" width="250"
                        height="250">
                    <h1 class="tracking-widest text-zinc-600 mt-2 text-sm">{{ strtoupper($product->name) }}</h1>
                    <p class="font-light text-zinc-400 text-xs">Rp {{ number_format($product->price, 2) }}</p>
                </div>
            @endforeach
        </div>
        <button class="bg-rose-300 px-4 py-2 mt-10 rounded text-white hover:bg-rose-400 grotesk"><a
                href="{{ route('login') }}">Shop Hijab</a></button>
    </div>
    <div class="w-full relative">
        <img src={{ asset('images/bannermid.png') }} alt="">
    </div>

    <!-- Add this section after the bestsellers section -->
    <div class="w-full h-screen flex justify-center items-center flex-col crimson my-10">
        <h1 class="text-3xl font-semibold mt-40 mb-2">Our Products</h1>
        <p>Explore our latest collection of hijabs</p>
        <div class="grid grid-cols-3 gap-4 mt-5 grotesk">
            @foreach ($products as $product)
                <div class="flex flex-col items-center text-center">
                    <img src="{{ route('product.image', $product->id) }}" alt="{{ $product->name }}"
                        class="w-60 h-60 object-cover mb-4 shadow-lg">
                    <h2 class="tracking-widest text-zinc-600 text-sm">
                        {{ strtoupper($product->name) }}
                    </h2>
                    <p class="font-light text-zinc-400 text-xs">
                        Rp {{ number_format($product->price, 2) }}
                    </p>
                </div>
            @endforeach
        </div>
        <button class="bg-rose-300 px-4 py-2 mt-5 rounded text-white hover:bg-rose-400 grotesk">
            <a href="{{ route('login') }}">
                View All Products
            </a>
        </button>
    </div>
    <div class="w-full h-screen flex justify-center items-end ">
        <div class="h-3/4 flex justify-center items-center bg-[#908571] crimson ">
            <div class="w-1/2 ml-40 text-zinc-200">
                <h1 class="text-4xl mb-5">Redefining Elegance and Modesty</h1>

                <p>Safana Hijab lebih dari sekadar produk, kami hadir untuk mendukung wanita muslim merayakan keindahan
                    dan nilai-nilai yang mereka yakini.

                    Tampil anggun, nyaman, dan tetap berkelas bersama Safana Hijab.</p>
            </div>
            <div class="w-1/2 mr-40 flex justify-center items-center relative">
                <img src={{ asset('images/kerudungbergo.jpg') }} alt="" width="300" height="300"
                    class="absolute -top-56 left-10 shadow-md">
                <img src={{ asset('images/ninja.jpg') }} alt="" width="300" height="300"
                    class=" absolute -top-20 left-60 shadow-md">
            </div>
        </div>
    </div>
    <footer class="footer bg-stone-300 text-zinc-700 p-10">
        <div class="w-3/4 ml-5">
            <h1 class="text-3xl font-semibold crimson">About Safana Hijab</h1>
            <p>
                Safana Hijab adalah merek fashion muslim yang menawarkan berbagai koleksi hijab modern, stylish, dan
                berkualitas tinggi.
            </p>
            <h6 class="footer-title mt-5">Social</h6>
            <div class="grid grid-flow-col gap-4">
                <a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        class="fill-current">
                        <path
                            d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z">
                        </path>
                    </svg>
                </a>
                <a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        class="fill-current">
                        <path
                            d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z">
                        </path>
                    </svg>
                </a>
                <a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                        class="fill-current">
                        <path
                            d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
        <div class="font-semibold mt-5">
            <p>Nama : Salsa Syifa Sabrina</p>
            <p>Nim : 10122157</p>
            <p>Kelas : IF - 5</p>
        </div>
    </footer>
</body>

</html>
