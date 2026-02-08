<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #f59e0b, #d97706); padding: 30px; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">Il tuo trial scade tra {{ $daysLeft }} giorni</h1>
    </div>
    
    <div style="background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
        <p style="font-size: 18px; margin-top: 0;">
            Ciao {{ $userName }},
        </p>
        
        <p>
            Il tuo periodo di prova gratuito di Adeguati Assetti Impresa sta per concludersi.
        </p>
        
        <p>
            <strong>Con il piano Pro mantieni:</strong>
        </p>
        
        <ul style="background: white; padding: 20px 30px; border-radius: 8px; margin-bottom: 20px;">
            <li style="margin-bottom: 10px;"><span style="color: #22c55e;">&#10003;</span> KPI settoriali ATECO</li>
            <li style="margin-bottom: 10px;"><span style="color: #22c55e;">&#10003;</span> Alert automatici email</li>
            <li style="margin-bottom: 10px;"><span style="color: #22c55e;">&#10003;</span> Report PDF esportabili</li>
            <li><span style="color: #22c55e;">&#10003;</span> Storico dati illimitato</li>
        </ul>
        
        <div style="background: #fef3c7; border: 1px solid #fcd34d; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0; font-weight: bold; color: #92400e;">
                Offerta speciale trial:
            </p>
            <p style="margin: 10px 0 0 0; color: #92400e;">
                Attiva il piano Pro entro la scadenza del trial e risparmia <strong>2 mesi</strong> con il piano annuale.
            </p>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $upgradeUrl }}" style="background: #2563eb; color: white; padding: 15px 40px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">
                Attiva Piano Pro - da 49 euro/mese
            </a>
        </div>
        
        <p style="color: #6b7280; font-size: 14px; text-align: center;">
            Oppure continua gratuitamente con il piano Free (solo 7 KPI base)
        </p>
    </div>
    
    <div style="padding: 20px; text-align: center; color: #6b7280; font-size: 12px;">
        <p>Adeguati Assetti Impresa - Smiledoc S.r.l.</p>
    </div>
</body>
</html>
