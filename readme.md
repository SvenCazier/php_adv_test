## LOGINGEGEVENS BESTAANDE KLANTEN

1. **gebruiker1@email.com**: wachtwoord1
2. **gebruiker2@email.com**: wachtwoord2 [^1]
3. **gebruiker3@email.com**: wachtwoord3
4. **gebruiker4@email.com**: wachtwoord4 [^1]
5. **gebruiker5@email.com**: wachtwoord5
6. **gebruiker6@email.com**: wachtwoord6
7. **gebruiker7@email.com**: wachtwoord7 [^1]
8. **gebruiker8@email.com**: wachtwoord8
9. **gebruiker9@email.com**: wachtwoord9 [^1]
10. **gebruiker10@email.com**: wachtwoord10

## WOORDJE UITLEG OVER REGISTRATIEPROCES

Om het bestelproces te weegspiegelen van bestaande (grote) sites waar klanten kunnen bestellen met dezelfde gegevens als gast, of verschillende accounts kunnen hebben met dezelfde gegevens:

Ik ken de interne keuken van hun databases niet natuurlijk, maar om duplicaten te vermijden per bestelling van een niet-regestreerde of niet-aangemelde klant, en toch toe te laten dat een gebruiker zich niet hoeft aan te melden om te bestellen[^2], wordt er geprobeerd een klant toe te voegen.

Dit beperkt de duplicaten tot 1x de klantgegevens zonder logingegevens, en 1x de klantgegevens per geregistreerd account.

Als de klantgegevens al bestaan zonder e-mailadres wordt dit record id teruggegeven dankzij "ON DUPLICATE KEY UPDATE id = LAST_INSERT_ID(id)" [^3] [^4] dankzij een unieke index op alle klantgegevens + een virtuele email kolom [^5] (zodat kan gegekeken worden naar de records waar het e-mailadres "null" is).

Indien deze nog niet bestaan zonder logingegevens wordt een record aangemaakt, omdat de gebruiker steeds als gast moet kunnen doorgaan, maar een bestelling natuurlijk wel over klantgegevens hoort te beschikken.

Als de klant zich dan probeert te registeren met een bestaand e-mail adres wordt er een foutmelding getoond, ~~bijkomend als dat e-mail adres bestaat bij dezelfde klantgegevens wordt de nieuwe record verwijderd[^6] en gevraagd om aan te melden[^7]~~, maar als een ander e-mailadres gebruikt wordt, wordt het record zonder e-mail en wachtwoord geupdated met de login gegevens als zijnde nieuw account.

## V2

Automatisch verwijderen van errors nadat ze getoond zijn.

-   Niet de taak van de controller om die te verwijderen.
-   Hoeven niet te blijven na een refresh.
-   Voorkomt problemen bij navigatie voordat ze verwijderd zijn.

Eventuele extra verbetering naar de toekomst toe: Verplaatsen van post afhandeling adres en account registratie naar aparte controller om de vervelende repost alert bij een refresh te voorkomen.

[^1]: Heeft recht op promotie.
[^2]: Achteraf bekeken had ik beter de scheiding tussen login en info behouden om het e-mailadres apart als info te kunnen opslaan (maar wordt uiteindelijk ook niet gevraagd hier).
[^3]: Voordeel: Er hoeft maar één query uitgevoerd te worden.
[^4]: Nadeel: iedere keer wordt de auto increment index verhoogd, maar de kans is klein dat ooit de limiet van 4294967295 UNSIGNED INTs bereikt wordt.
[^5]: MySQL beschouwt NULL niet als uniek, en ik wou de integriteit van de email kolom niet beschadigen door een andere waarde dan een e-mail of NULL toe te laten.
[^6]: Mag niet verwijderd worden, omdat het nodig is om een nieuw account aan te maken.
[^7]: Zou informatie weggeven.
