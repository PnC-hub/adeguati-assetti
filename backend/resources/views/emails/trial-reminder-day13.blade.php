<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: linear-gradient(135deg, #dc2626, #991b1b); padding: 30px; border-radius: 10px 10px 0 0;">
        <h1 style="color: white; margin: 0; font-size: 24px;">ULTIMO GIORNO: Il tuo trial scade domani</h1>
    </div>
    
    <div style="background: #f9fafb; padding: 30px; border: 1px solid #e5e7eb; border-top: none;">
        <p style="font-size: 18px; margin-top: 0;">
            {{ $userName }},
        </p>
        
        <p>
            <strong>Domani perderai accesso a:</strong>
        </p>
        
        <ul style="background: #fef2f2; padding: 20px 30px; border-radius: 8px; margin-bottom: 20px;">
            <li style="margin-bottom: 10px; color: #dc2626;"><span style="text-decoration: line-through;">KPI settoriali ATECO</span></li>
            <li style="margin-bottom: 10px; color: #dc2626;"><span style="text-decoration: line-through;">Alert automatici email</span></li>
            <li style="margin-bottom: 10px; color: #dc2626;"><span style="text-decoration: line-through;">Report PDF esportabili</span></li>
            <li style="color: #dc2626;"><span style="text-decoration: line-through;">Storico oltre 3 mesi</span></li>
        </ul>
        
        <p>
            Con il piano <strong>Free</strong> avrai accesso solo ai 7 KPI obbligatori base.
        </p>
        
        <div style="background: #dcfce7; border: 2px solid #22c55e; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <p style="margin: 0; font-weight: bold; color: #166534; font-size: 18px; text-align: center;">
                Attiva ORA il Piano Pro
            </p>
            <p style="margin: 10px 0 0 0; color: #166534; text-align: center;">
                Solo <strong>49 euro/mese</strong> - meno di un caffe al giorno per proteggere la tua azienda
            </p>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $upgradeUrl }}" style="background: #22c55e; color: white; padding: 18px 50px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; font-size: 18px;">
                Mantieni le Funzionalita Pro
            </a>
        </div>
        
        <p style="color: #6b7280; font-size: 14px; text-align: center;">
            Hai domande? Rispondi a questa email.
        </p>
    </div>
    
    <div style="padding: 20px; text-align: center; color: #6b7280; font-size: 12px;">
        <p>Adeguati Assetti Impresa - Smiledoc S.r.l.</p>
    </div>
</body>
</html>
