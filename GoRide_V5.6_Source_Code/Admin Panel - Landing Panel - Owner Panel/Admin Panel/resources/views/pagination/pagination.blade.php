@if ($paginator->hasPages())
     <div style="text-align: center;">
          <p class="text-sm text-gray-700 leading-5 mt-3">
          {{trans('lang.showing')}}
          <span class="font-medium">{{ $paginator->firstItem() }}</span>
          {{trans('lang.to')}}
          <span class="font-medium">{{ $paginator->lastItem() }}</span>
          {{trans('lang.of')}}
          <span class="font-medium">{{ $paginator->total() }}</span>
          {{trans('lang.results')}}
          </p>
     </div>
@endif