<!DOCTYPE html>
<html>
<head>
    <title>یادآوری رزرو هتل</title>
</head>
<body>
    <h1>درود بر شما : {{ $reservation->user->name }}</h1>
    <p>اتاقی که رزور کردید {{$reservation->room->room_type}}</p>
    <p>در هتل : {{$reservation->room->hotel->name}}</p>
    <p>تاریخ شروع رزور : {{$reservation->check_in_date}}</p>
    <p>تاریخ اتمام رزور : {{$reservation->check_out_date}}</p>
</body>
</html>
