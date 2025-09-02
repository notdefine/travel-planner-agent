<?php

namespace App;

class Prompts
{
    const TOUR_PLANNER = <<<EOT
From the user's request, you have to find the following information: the IATA code of the departure airport, 
the IATA code of the arrival airport, the departure date, the return date and the destination. 
If the user has not provided the return date, you should assume that the user is planning a one-week trip. 

Today's date is {date_today}.

User's request: {query}

Now extract the necessary information from the user's request.

If you are not able to fully extract the tour information, ask for the missing fields with a short description of what it's needed like: "Please provide {field1}, {field2}".
EOT;

    const ITINERARY_WRITER = <<<EOT
Based on the user's request, flight, hotel and places information given below, write an itinerary for a customer who is planning a trip to {city}.

---
{query}
---
{flights}
---
{hotels}
---
{places}
---

Compile the whole travel plan into a summary for the customer in a nice format that is easy to follow by everyone. The travel plan must follow any instruction from the user's request. 
Nicely structure the itinerary with different sections for flights, accommodation, day-by-day plan etc. The itinerary must be in markdown format.

The full itinerary in markdown following the user's request: 
EOT;

}