<?php
// Edit freely: keep the quotes and commas.
// Format:  'Location name' => ['City 1', 'City 2'],

// Sites and offices
$SITES = [
  'Head Office - Lagos' => ['Lagos'],
  'Apapa Port'          => ['Apapa','Lagos'],
  'Lekki Free Zone'     => ['Lekki','Lagos'],
  'Ibese Plant'         => ['Ibese'],
  'Obajana Plant'       => ['Obajana'],
  'Gboko Plant'         => ['Gboko'],
  'Okpella Plant'       => ['Okpella'],
  'Ankpa Coal Mine'     => ['Ankpa'],
  'Ndola Plant'         => ['Ndola'],
  'Mtwara Plant'        => ['Mtwara'],
];

// East Africa
$EAST = [
  'Kenya'       => ['Nairobi','Mombasa','Kisumu','Nakuru','Eldoret','Thika','Malindi','Nyeri','Machakos','Naivasha','Garissa','Kitale'],
  'Uganda'      => ['Kampala','Entebbe','Jinja','Gulu','Mbarara','Mbale'],
  'Tanzania'    => ['Dar es Salaam','Dodoma','Arusha','Mwanza','Mtwara','Zanzibar','Mbeya','Tanga'],
  'Rwanda'      => ['Kigali','Butare','Gisenyi','Musanze'],
  'Ethiopia'    => ['Addis Ababa','Dire Dawa','Hawassa','Bahir Dar','Mekelle'],
  'Burundi'     => ['Bujumbura','Gitega'],
  'South Sudan' => ['Juba','Wau','Malakal'],
  'Somalia'     => ['Mogadishu','Hargeisa','Kismayo'],
];

// Other countries
$OTHER = [
  'Nigeria'          => ['Lagos','Apapa','Abuja','Ibese','Obajana','Gboko','Okpella','Ankpa','Lekki','Port Harcourt','Kano','Ibadan','Enugu','Onne'],
  'Zambia'           => ['Lusaka','Ndola','Kitwe'],
  'DR Congo'         => ['Kinshasa','Lubumbashi','Goma'],
  'Congo-Brazzaville'=> ['Brazzaville','Pointe-Noire'],
  'Ghana'            => ['Accra','Tema','Kumasi'],
  'Cameroon'         => ['Douala','Yaounde'],
  'Senegal'          => ['Dakar'],
];


// Map pin positions [latitude, longitude]. Add a line for any city you add above.
$COORDS = [
  'Lagos'=>[6.5244,3.3792],'Apapa'=>[6.4489,3.3597],'Lekki'=>[6.4698,3.5852],'Ibese'=>[6.85,2.98],
  'Obajana'=>[7.93,6.43],'Gboko'=>[7.3253,9.0043],'Okpella'=>[7.32,6.25],'Ankpa'=>[7.3706,7.6275],
  'Abuja'=>[9.0765,7.3986],'Port Harcourt'=>[4.8156,7.0498],'Kano'=>[12.0022,8.5919],'Ibadan'=>[7.3775,3.9470],
  'Enugu'=>[6.4584,7.5464],'Onne'=>[4.7167,7.15],'Ndola'=>[-12.9587,28.6366],'Lusaka'=>[-15.3875,28.3228],
  'Kitwe'=>[-12.8024,28.2132],'Kinshasa'=>[-4.4419,15.2663],'Lubumbashi'=>[-11.6876,27.5026],'Goma'=>[-1.6792,29.2228],
  'Brazzaville'=>[-4.2634,15.2429],'Pointe-Noire'=>[-4.7761,11.8635],'Accra'=>[5.6037,-0.1870],'Tema'=>[5.6698,-0.0166],
  'Kumasi'=>[6.6885,-1.6244],'Douala'=>[4.0511,9.7679],'Yaounde'=>[3.8480,11.5021],'Dakar'=>[14.7167,-17.4677],
  'Nairobi'=>[-1.2921,36.8219],'Mombasa'=>[-4.0435,39.6682],'Kisumu'=>[-0.0917,34.7680],'Nakuru'=>[-0.3031,36.0800],
  'Eldoret'=>[0.5143,35.2698],'Thika'=>[-1.0332,37.0693],'Malindi'=>[-3.2192,40.1169],'Nyeri'=>[-0.4201,36.9476],
  'Machakos'=>[-1.5177,37.2634],'Naivasha'=>[-0.7172,36.4310],'Garissa'=>[-0.4532,39.6461],'Kitale'=>[1.0157,35.0062],
  'Kampala'=>[0.3476,32.5825],'Entebbe'=>[0.0512,32.4637],'Jinja'=>[0.4244,33.2041],'Gulu'=>[2.7746,32.2990],
  'Mbarara'=>[-0.6072,30.6545],'Mbale'=>[1.0821,34.1751],'Dar es Salaam'=>[-6.7924,39.2083],'Dodoma'=>[-6.1630,35.7516],
  'Arusha'=>[-3.3869,36.6830],'Mwanza'=>[-2.5164,32.9175],'Mtwara'=>[-10.2736,40.1824],'Zanzibar'=>[-6.1659,39.2026],
  'Mbeya'=>[-8.9094,33.4608],'Tanga'=>[-5.0689,39.0986],'Kigali'=>[-1.9441,30.0619],'Butare'=>[-2.5966,29.7394],
  'Gisenyi'=>[-1.7028,29.2564],'Musanze'=>[-1.4998,29.6350],'Addis Ababa'=>[9.03,38.74],'Dire Dawa'=>[9.5931,41.8661],
  'Hawassa'=>[7.0504,38.4955],'Bahir Dar'=>[11.5936,37.3908],'Mekelle'=>[13.4967,39.4753],'Bujumbura'=>[-3.3614,29.3599],
  'Gitega'=>[-3.4271,29.9246],'Juba'=>[4.8594,31.5713],'Wau'=>[7.7027,28.0044],'Malakal'=>[9.5334,31.6605],
  'Mogadishu'=>[2.0469,45.3182],'Hargeisa'=>[9.56,44.065],'Kismayo'=>[-0.3582,42.5454],
];