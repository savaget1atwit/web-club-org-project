<!DOCTYPE html>
<html>
    <html lang = "en">

<head>
    <meta charset = "UTF-8">
</head>

<body>
     <!-- could have feature: 
        customization of color schemes based on club
        nfc id reader 
        remember me option-->


    <!-- Login portal for clubs to enter the organization their affliated with, ID to attach to org roles
    and password(?) -->
    <section id="club_login_form">
        <!-- org, name/id, password -->
        <Form id="club_login_portal" action = "home.php" method = "post">
            <h1>Please log in to access your org's information...</h1>
            
            <!-- autocomplete possibly after certain amount of characters -->
            <label for = "form_org_input">Affiliated Organization</label><br>
            <input type = text id = "form-org-input" required><br><br>


            <!-- general body able type in org and press 'just view as general body' -->
            <label for ="wID">WIT ID:</label><br>
            <input type = "text" id = "wID" pattern = "[W]{1}\d{8}" required><br><br>

            <label for ="password">Password:</label><br>
            <input type = "password" id = "pass" name = "pass" minLength = "4" required><br><br>

            <button type ="submit" id ="submit">Submit</button>
         </Form>
    </section>

</html>