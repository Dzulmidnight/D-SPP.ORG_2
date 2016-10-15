        <html>
          <head>
            <meta charset="utf-8">
          </head>
          <body>
      <table style="font-family: Tahoma, Geneva, sans-serif; font-size: 13px; color: #797979;" border="0" width="650px">
        <tbody>
          <tr>
            <th rowspan="4" scope="col" align="center" valign="middle" width="170"><img src="http://d-spp.org/img/mailFUNDEPPO.jpg" alt="Simbolo de Pequeños Productores." width="120" height="120" /></th>
            <th scope="col" align="left" width="280" ><strong>Notificación de Cotización / Price Notification</strong></th>
          </tr>
          <tr>
            <td align="left" style="color:#ff738a;">Email Organismo de Certificación / Certification Entity: '.$oc['email1'].'</td>
          </tr>

          <tr>
            <td align="left">'.$oc['pais'].'</td>
          </tr>
          <tr>
            <td aling="left" style="text-align:justify">
            Se ha enviado la cotización correspondiente a la Solicitud de Certificación para Organizaciones de Pequeños Productores.
            <br><br> Por favor iniciar sesión en el siguiente enlace <a href="http://d-spp.org/">www.d-spp.org/</a> como OPP, para poder acceder a la cotización.

            <br><br>
            The quotation corresponding to the Certification Application for Small producers organizations has been sent.
              <br><br>Please log in to the following link <a href="http://d-spp.org/?OPP">www.d-spp.org/</a> as OPP to access the quotation.

            </td>
          </tr>

          <tr>
            <td colspan="2">
              <table style="font-family: Tahoma, Geneva, sans-serif; color: #797979; margin-top:10px; margin-bottom:20px;" border="1" width="650px">
                <tbody>
                  <tr style="font-size: 12px; text-align:center; background-color:#dff0d8; color:#3c763d;" height="50px;">
                    <td width="130px">Nombre de la organización/Organization name</td>
                    <td width="130px">País / Country</td>
                    <td width="130px">Organismo de Certificación / Certification Entity</td>
                    <td width="130px">Fecha de envío / Shipping Date</td>
                 
                    
                  </tr>
                  <tr style="font-size: 12px; text-align:justify">
                    <td style="padding:10px;">
                      '.$_POST['nombre'].'
                    </td>
                    <td style="padding:10px;">
                      '.$_POST['pais'].'
                    </td>
                    <td style="padding:10px;">
                      '.$oc['nombre'].'
                    </td>
                    <td style="padding:10px;">
                    '.date('d/m/Y', $fecha).'
                    </td>
                  </tr>

                </tbody>
              </table>        
            </td>
          </tr>
                <tr>
                  <td colspan="2">
                    <span style="color:red">¿Qué es lo de debo realizar ahora?. Debes "Aceptar" o "Rechazar" la cotización</span>
                    <ol>
                      <li>Debes iniciar sesión dentro del sistema <a href="http://d-spp.org/">D-SPP (clic aquí)</a> como Organización de Pequeños Productores(OPP).</li>
                      <li>Dentro de tu cuenta debes seleccionar Solicitudes > Listado Solicitudes.</li>
                      <li>Dentro de la tabla solicitudes debes localizar la columna "Cotización" Y seleccionar el botón Verde (aceptar cotización) ó el botón Rojo (rechazar cotización)</li>
                      <li>En caso de aceptar la cotización debes esperar a que finalice el "Periodo de Objeción"</li>
                    </ol>
                  </td>
                </tr> 
                <tr>
                  <td coslpan="2">Para cualquier duda o aclaración por favor contactar a: soporte@d-spp.org</td>
                </tr>
        </tbody>
      </table>

          </body>
          </html>