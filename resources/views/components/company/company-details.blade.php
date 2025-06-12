 @php
     $dateFields = [
         'Effective Date',
         'Registration Date',
         'Last Annual Return',
         'Next Annual Return',
         'Next Financial Statement Due',
         'Last Accounts',
         'Last AGM',
         'Financial Year End',
     ];

     $details = [
         'Company Number' => $company->company_number,
         'Custom Name' => $company->custom,
         'Company Type' => $company->company_type_code . ' | ' . $company->company_type,
         'Status' => $company->status,
         'Status Code' => $company->company_status_code,
         'Effective Date' => $company->effective_date,
         'Registration Date' => $company->registration_date,
         'Last Annual Return' => $company->last_annual_return,
         'Next Annual Return' => $company->next_annual_return,
         'Next Financial Statement Due' => $company->next_financial_statement_due,
         'Last Accounts' => $company->last_accounts,
         'Last AGM' => $company->last_agm,
         'Financial Year End' => $company->financial_year_end,
         'Place of Business' => $company->place_of_business,
         'Address' => $company->postcode . ' | ' . $company->address_line_1 . ' , ' . $company->address_line_2,
         'Address Line 3' => $company->address_line_3,
         'Address Line 4' => $company->address_line_4,
     ];
 @endphp

 <div>
     <div class="px-4 sm:px-0">
         <h3 class="text-base/7 font-semibold text-gray-900">{{ $company->name }}</h3>
         <p class="mt-1 max-w-2xl text-sm/6 text-gray-500">Company details.</p>
     </div>
     <div class="mt-6 border-t border-gray-100">

         <dl class="divide-y divide-gray-100">


             @foreach ($details as $label => $value)
                 @if (!empty($value))
                     <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                         <dt class="text-sm/6 font-medium text-gray-900">{{ $label }}</dt>
                         <dd class="mt-1 text-sm/6 text-gray-700 sm:col-span-2 sm:mt-0">
                             @if (in_array($label, $dateFields))
                                 {{ \Carbon\Carbon::parse($value)->format('Y-m-d') }}
                             @else
                                 {{ $value }}
                             @endif
                         </dd>
                     </div>
                 @endif
             @endforeach
         </dl>

         <dl class="divide-y divide-gray-100">
             <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                 <dt class="text-sm/6 font-medium text-gray-900">Attachments</dt>
                 <dd class="mt-2 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                     <ul role="list" class="divide-y divide-gray-100 rounded-md border border-gray-200">
                         {{-- <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm/6">
                             <div class="flex w-0 flex-1 items-center">
                                 <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                     aria-hidden="true" data-slot="icon">
                                     <path fill-rule="evenodd"
                                         d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z"
                                         clip-rule="evenodd" />
                                 </svg>
                                 <div class="ml-4 flex min-w-0 flex-1 gap-2">
                                     <span class="truncate font-medium">resume_back_end_developer.pdf</span>
                                     <span class="shrink-0 text-gray-400">2.4mb</span>
                                 </div>
                             </div>
                             <div class="ml-4 shrink-0">
                                 <a href="#"
                                     class="font-medium text-indigo-600 hover:text-indigo-500">Download</a>
                             </div>
                         </li>
                         <li class="flex items-center justify-between py-4 pl-4 pr-5 text-sm/6">
                             <div class="flex w-0 flex-1 items-center">
                                 <svg class="size-5 shrink-0 text-gray-400" viewBox="0 0 20 20" fill="currentColor"
                                     aria-hidden="true" data-slot="icon">
                                     <path fill-rule="evenodd"
                                         d="M15.621 4.379a3 3 0 0 0-4.242 0l-7 7a3 3 0 0 0 4.241 4.243h.001l.497-.5a.75.75 0 0 1 1.064 1.057l-.498.501-.002.002a4.5 4.5 0 0 1-6.364-6.364l7-7a4.5 4.5 0 0 1 6.368 6.36l-3.455 3.553A2.625 2.625 0 1 1 9.52 9.52l3.45-3.451a.75.75 0 1 1 1.061 1.06l-3.45 3.451a1.125 1.125 0 0 0 1.587 1.595l3.454-3.553a3 3 0 0 0 0-4.242Z"
                                         clip-rule="evenodd" />
                                 </svg>
                                 <div class="ml-4 flex min-w-0 flex-1 gap-2">
                                     <span class="truncate font-medium">coverletter_back_end_developer.pdf</span>
                                     <span class="shrink-0 text-gray-400">4.5mb</span>
                                 </div>
                             </div>
                             <div class="ml-4 shrink-0">
                                 <a href="#"
                                     class="font-medium text-indigo-600 hover:text-indigo-500">Download</a>
                             </div>
                         </li> --}}
                     </ul>
                 </dd>
             </div>
         </dl>

         <dl class="divide-y divide-gray-100">
             <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                 <dt class="text-sm/6 font-medium text-gray-900">Actions</dt>
                 <dd class="mt-2 text-sm text-gray-900 sm:col-span-2 sm:mt-0">
                     <div class="flex flex-col gap-2 text-right">
                         <flux:button wire:click="$set('companyDetailsModal', false)"
                             wire:keydown.escape.window="$set('companyDetailsModal', false)">
                             Close
                         </flux:button>
                     </div>
                 </dd>
             </div>
         </dl>

     </div>
 </div>
