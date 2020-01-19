<h1>{{ concert->title }}</h1>
<h1>{{ concert->subtitle }}</h1>
<h1>{{ concert->formatted_date }}</h1>
<h1>Doors at {{ $concert->formatted_start_time }}</h1>
<h1>{{ concert->ticket_price_in_dollars }}</h1>
<h1>{{ concert->venue }}</h1>
<h1>{{ concert->venue_address }}</h1>
<h1>{{ concert->city }} {{ concert->state }} {{ concert->zip }}</h1>
<h1>{{ concert->additional_information }}</h1>