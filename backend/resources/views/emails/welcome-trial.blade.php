<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benvenuto in Adeguati Assetti Impresa</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #2563eb, #1d4ed8); padding: 30px; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Benvenuto, {{ $userName }}!</h1>
    </div>
    
    <div style="background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
        <p style="font-size: 16px; margin-bottom: 20px;">
            Grazie per esserti registrato su <strong>Adeguati Assetti Impresa</strong>.
        </p>
        
        <p style="margin-bottom: 20px;">
            Hai <strong>{{ $trialDays }} giorni</strong> per provare tutte le funzionalita Pro:
        </p>
        
        <ul style="background: white; padding: 20px 30px; border-radius: 8px; margin-bottom: 20px;">
            <li style="margin-bottom: 10px;">Tutti i <strong>7 KPI obbligatori CNDCEC</strong></li>
            <li style="margin-bottom: 10px;">KPI settoriali per il tuo codice ATECO</li>
            <li style="margin-bottom: 10px;">Alert automatici via email</li>
            <li style="margin-bottom: 10px;">Report PDF esportabili</li>
            <li>Storico dati illimitato</li>
        </ul>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $dashboardUrl }}" style="background: #2563eb; color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">
                Vai alla Dashboard
            </a>
        </div>
        
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin-top: 20px;">
            <p style="margin: 0; color: #92400e;">
                <strong>Primo passo consigliato:</strong> Inserisci i dati economici del mese corrente per calcolare il tuo primo score di salute aziendale.
            </p>
        </div>
    </div>
    
    <div style="padding: 20px; text-align: center; color: #6b7280; font-size: 12px;">
        <p>Adeguati Assetti Impresa - Smiledoc S.r.l.</p>
        <p>Via Gianni Metello 37, 51100 Pistoia (PT) - P.IVA IT15131801001</p>
    </div>
</body>
</html>
