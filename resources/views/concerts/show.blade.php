<h1>{{ concert->title }}</h1>
<h1>{{ concert->subtitle }}</h1>
<h1>{{ concert->date->format('F j, Y') }}</h1>
<h1>Doors at {{ $concert->date->format('g:ia') }}</h1>
<h1>{{ number_format(concert->ticket_price / 100, 2 }}</h1>
<h1>{{ concert->venue }}</h1>
<h1>{{ concert->venue_address }}</h1>
<h1>{{ concert->city }} {{ concert->state }} {{ concert->zip }}</h1>
<h1>{{ concert->additional_information }}</h1>