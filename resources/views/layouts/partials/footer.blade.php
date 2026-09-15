<footer id="contact" class="bg-white py-16 px-6 md:px-12 lg:px-20 text-gray-600">
  <div class="max-w-7xl mx-auto grid md:grid-cols-4 gap-12">
    
    <!-- Column 1: Company Info -->
    <div>
      <div class="flex items-center gap-1 mb-4">
        <img src="{{ asset($global['site_logo']) }}" alt="{{ $global['site_name'] }}" class="h-16 w-auto object-contain">
      </div>

      @if(!empty($global['site_tag']))
        <p class="text-sm mb-6">{{ $global['site_tag'] }}</p>
      @endif

      @if(!empty($global['email']))
        <p class="text-sm mb-2">
          <span class="font-medium text-black">Email:</span>
          <a href="mailto:{{ $global['email'] }}" class="hover:text-pink-500">{{ $global['email'] }}</a>
        </p>
      @endif

      @if(!empty($global['phone']))
        <p class="text-sm mb-2">
          <span class="font-medium text-black">Phone:</span>
          <a href="tel:{{ $global['phone'] }}" class="hover:text-pink-500">{{ $global['phone'] }}</a>
        </p>
      @endif

      @if(!empty($global['address']))
        <p class="text-sm">
          <span class="font-medium text-black">Office Address:</span> {{ $global['address'] }}
        </p>
      @endif

      <div class="flex gap-4 text-xl text-gray-400 mt-4">
        @foreach(['facebook','twitter','instagram','linkedin'] as $social)
          @if(!empty($global[$social]))
            <a href="{{ $global[$social] }}" target="_blank" class="hover:text-pink-500">
              <i class="fab fa-{{ $social === 'linkedin' ? 'linkedin-in' : $social }}"></i>
            </a>
          @endif
        @endforeach
      </div>
    </div>

    <!-- Column 2: Product -->
    <div>
      <h4 class="font-bold text-gray-800 mb-3 uppercase text-sm">Product</h4>
      <ul class="space-y-2 text-sm">
  <li><a href="{{ route('home') }}/#distribution" class="hover:text-pink-500">Distribution</a></li>

  @if(!empty($global['require_subscription']) && $global['require_subscription'])
    <li><a href="{{ route('home') }}/#pricing" class="hover:text-pink-500">Pricing</a></li>
  @endif

  <li><a href="{{ route('home') }}/#artists" class="hover:text-pink-500">Artists</a></li>
  <li><a href="{{ route('home') }}/#contact" class="hover:text-pink-500">Contact</a></li>
</ul>

    </div>

    <!-- Column 3: Company -->
    <!--<div>-->
    <!--  <h4 class="font-bold text-gray-800 mb-3 uppercase text-sm">Company</h4>-->
    <!--  <ul class="space-y-2 text-sm">-->
    <!--    <li><a href="{{ route('home') }}/#about" class="hover:text-pink-500">About</a></li>-->
    <!--    <li><a href="{{ route('home') }}/#blog" class="hover:text-pink-500">Blog</a></li>-->
    <!--    <li><a href="{{ route('home') }}/#careers" class="hover:text-pink-500">Careers</a></li>-->
    <!--    <li><a href="{{ route('home') }}/#support" class="hover:text-pink-500">Support</a></li>-->
    <!--  </ul>-->
    <!--</div>-->

    <!-- Column 4: Legal -->
    <div>
      <h4 class="font-bold text-gray-800 mb-3 uppercase text-sm">Legal</h4>
      <ul class="space-y-2 text-sm">
        <li><a href="{{ route('privacy.policy') }}" class="hover:text-pink-500">Privacy Policy</a></li>
        <li><a href="{{ route('terms') }}" class="hover:text-pink-500">Terms of Service</a></li>
        <li><a href="{{ route('cookies.page') }}" class="hover:text-pink-500">Cookie Policy</a></li>
        <li><a href="{{ route('refund.page') }}" class="hover:text-pink-500">Refund Policy</a></li>
      </ul>
    </div>
  </div>

  <div class="border-t border-gray-200 mt-12 pt-6 text-sm text-gray-400 flex flex-col md:flex-row justify-between items-center">
    <p>&copy; {{ now()->year }} {{ $global['site_name'] }}. All Rights Reserved.</p>
    <p>{{ $global['footer_text'] ?? 'Made for creators, by creators.' }}</p>
  </div>
</footer>
