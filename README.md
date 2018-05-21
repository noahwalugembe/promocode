# promocode

Task: Implement a promo code api with the following features. 
●	Generation of new promo codes for events
●	The promo code is worth a specific amount of ride
●	The promo code can expire
●	Can be deactivated
●	Return active promo codes
●	Return all promo codes
●	Only valid when user’s pickup or destination is within x radius of the event venue
●	The promo code radius should be configurable 
●	To test the validity of the promo code, expose an endpoint that accept origin, destination, the promo code. 
    The api should return the promo code details and a polyline using the destination and origin 
	if promo code is valid and an error otherwise. 

	
.......................
#Application  endpoints developed
................................
http://localhost/SafeBoda_promo/promocode/v1/Api.php?apicall=createcode(Generation of new promo codes for events,The promo code is worth a specific amount of ride,The promo code can expire)
http://localhost/SafeBoda_promo/promocode/v1/Api.php?apicall=getcode(Return all promo codes)
http://localhost/SafeBoda_promo/promocode/v1/Api.php?apicall=validatecode (test the validity of the promo code basing on pickup or destination within x radius of the event venue )
http://localhost/SafeBoda_promo/promocode/v1/Api.php?apicall=getactive (Return active promo codes)
http://localhost/SafeBoda_promo/promocode/v1/Api.php?apicall=updatecode(The promo code radius should be configurable)
http://localhost/SafeBoda_promo/promocode/v1/Api.php?apicall=redeemcode(one devcice can redeem a code once  basing on pickup or destination within x radius of the event venue  )
http://localhost/SafeBoda_promo/promocode/v1/Api.php?apicall=deletecode (Promocode can be deleted)

..........................................
	safeboda_promo_code.sql(database file)