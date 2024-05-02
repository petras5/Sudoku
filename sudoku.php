<?php
/*
	Rješenje DZ1 - Igre Sudoku pomoću objektno-orijentiranog programiranja.
*/

class Kvadratic
{
    public int $broj;
    public string $boja;

    public function __construct(int $broj, string $boja = '')
    {
        $this->broj = $broj;
        $this->boja = $boja;
    }
}

function osmisliPlocu(): array {
    $ploca = [];
    for ($i = 0; $i < 6; $i++) {
        $ploca_row = [];
        for ($j = 0; $j < 6; $j++) 
        {
            $ploca_row[] = null;
        }
        $ploca[] = $ploca_row;
    }
    $ploca[0][2] = new Kvadratic(4, 'crna');
    $ploca[1][3] = new Kvadratic(2, 'crna');
    $ploca[1][4] = new Kvadratic(3, 'crna');
    $ploca[2][0] = new Kvadratic(3, 'crna');
    $ploca[2][4] = new Kvadratic(6, 'crna');
    $ploca[3][1] = new Kvadratic(6, 'crna');
    $ploca[3][5] = new Kvadratic(2, 'crna');
    $ploca[4][1] = new Kvadratic(2, 'crna');
    $ploca[4][2] = new Kvadratic(1, 'crna');
    $ploca[5][3] = new Kvadratic(5, 'crna');

    return $ploca;
}


class RijesiSudoku
{
    protected $imeIgraca, $brojPokusaja, $gameOver, $ploca;
    protected $errorMsg;

	const IGRA_IDE_DALJE = 1, POBJEDA = 0, KRENI_ISPOCETKA = -1;

    function __construct()
    {
        $this->imeIgraca = false;
        $this->brojPokusaja = 0;
        $this->ploca = osmisliPlocu(); // Generiramo plocu koju treba rijesiti
        $this->gameOver = false;
        $this->errorMsg = false;
    }

    function ispisiFormuZaIme()
    {
        // Ispisi formu koja ucitava ime igraca
        ?>

        <!DOCTYPE html>
        <html>
        <head>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <meta charset="utf-8">
            <title>Sudoku</title>
            <style>
                body {
                    font-family: "Poppins", sans-serif;
                    font-weight: 400;
                    font-style: normal;
                }
            </style>
        </head>
        <body>
        <h1>Sudoku 6x6!</h1>
        <form method="post" action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
            Unesi svoje ime: <input type="text" name="imeIgraca" />
            <button type="submit">Započni igru!</button>
        </form>

        <?php if( $this->errorMsg !== false ) echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>'; ?>
        </body>
        </html>

        <?php
    }


    function ispisiFormuZaUpisivanjeBroja( $prethodniPokusaj )
    {
        // Ispisuje formu za pogađanje broja + poruku o prethodnom pokušaju.
        // Povećaj brojač pokušaja -- brojim sad i neuspješne pokušaje.
        ++$this->brojPokusaja;

        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <meta charset="utf-8">
            <title>Sudoku - Probaj riješiti!</title>
            <style>
                body {
                    font-family: "Poppins", sans-serif;
                    font-weight: 400;
                    font-style: normal;
                }
                table {
                    border: 2px solid;
                    border-collapse: collapse;
                }
                tr, td {
                    border: 1px solid grey;
                }
                tr:nth-child(even){
                    border-bottom: 2px solid black;
                }
                td:nth-child(3n){
                    border-right: 2px solid black;
                }
                td {
                    width: 50px;
                    height: 50px;
                }
                td p {
                    margin: 0;
                    text-align: center;
                    font-size: 2em;
                }
                td p.crna {
                    font-weight: bold;
                }
                td p.plava {
                    color: blue;
                }
                td p.crvena {
                    color: red;
                }
            </style>
        </head>
        <body>
        <h1>Sudoku 6x6!</h1>
        <p>
            Igrač: <?php echo htmlentities( $this->imeIgraca ); ?>
            <br>
            Broj pokušaja: <?php echo $this->brojPokusaja; ?>
        </p>
        <?php
        echo '<table>';
            foreach ($this->ploca as $row) 
            {
            echo '<tr>';
                foreach ($row as $element) 
                {
                    echo '<td>';
                    if ($element === null) 
                    {
                        echo " ";
                    } 
                    else 
                    {
                        echo "<p class=\"$element->boja\">";
                        echo $element->broj;
                        echo "</p>";
                    }
                    echo '</td>';
                }
            echo '</tr>';
            }
        echo '</table>';
        ?>
        <br>
        <br>
        <form method="post" action="<?php echo htmlentities( $_SERVER['PHP_SELF']); ?>">
            <input type="radio" name="action_option" value="input_number"> Unesi broj <input type="text" name="number" />
            u redak 
            <select name="redak" id="redak">
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
                <option value="6">6</option>
            </select>
                i stupac 
                <select name="stupac" id="stupac">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
                <br>
                <br>
                <input type="radio" name="action_option" value="delete_number"> Obriši broj iz retka
                <select name="izbrisi_redak" id="izbrisi_redak">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
                i stupca 
                <select name="izbrisi_stupac" id="izbrisi_stupac">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                    <option value="4">4</option>
                    <option value="5">5</option>
                    <option value="6">6</option>
                </select>
                <br>
                <br>
                <input type="radio" name="action_option" value="start_over"> Želim sve ispočetka!
                <br>
                <br>
                <button type="submit">Izvrši akciju!</button>
        </form>
        <?php if( $this->errorMsg !== false ) echo '<p>Greška: ' . htmlentities( $this->errorMsg ) . '</p>'; ?>
        </body>
        </html>
        <?php
    }


    function ispisiCestitku()
    {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
            <meta charset="utf-8">
            <title>Bravo!</title>
            <style>
                body {
                    font-family: "Poppins", sans-serif;
                    font-weight: bold;
                    font-style: normal;
                }
            </style>
        </head>
        <body>
        <p>
            Bravo, <?php echo htmlentities( $this->imeIgraca ); ?>!
            <br>
            Riješen sudoku u <?php echo $this->brojPokusaja; ?> pokušaja.
        </p>
        </body>
        </html>
        <?php
    }


    function get_imeIgraca()
    {
        // Je li već definirano ime igrača?
        if( $this->imeIgraca !== false )
            return $this->imeIgraca;

        // Možda nam se upravo sad šalje ime igrača?
        if( isset( $_POST['imeIgraca'] ) )
        {
            // Šalje nam se ime igrača. Provjeri da li se sastoji samo od slova.
            if( !preg_match( '/^[a-zA-Z]{1,20}$/', $_POST['imeIgraca'] ) )
            {
                // Nije dobro ime. Dakle nemamo ime igrača.
                $this->errorMsg = 'Ime igrača treba imati između 1 i 20 slova.';
                return false;
            }
            else
            {
                // Dobro je ime. Spremi ga u objekt.
                $this->imeIgraca = $_POST['imeIgraca'];
                return $this->imeIgraca;
            }
        }
        // Ne šalje nam se sad ime. Dakle nemamo ga uopće.
        return false;
    }

    // trazi ima li jednakog broja unesenom u manjem pravokutniku dimenzije 2x3
    function pretrazivanjeManjegPravokutnika($uneseniBroj, $uneseniRedak, $uneseniStupac, $pocetniRedak, $pocetniStupac)
    {
        $pronasaoIsti = false;
        for($i = $pocetniRedak; $i <= $pocetniRedak+1; $i++)
        {
            for($j = $pocetniStupac; $j <= $pocetniStupac+2; $j++)
            {
                if($i === $uneseniRedak and $j === $uneseniStupac)
                    continue;

                elseif($this->ploca[$i][$j]!== null and $this->ploca[$i][$j]->broj===$uneseniBroj)
                    $pronasaoIsti = true;
            }
        }
        return $pronasaoIsti;
    }

    function setColor(&$trenutni, $redak, $stupac)
    {
        for($s = 0;$s < 6; $s++ )
        {
            if($s === $stupac)
                continue;

            if($this->ploca[$redak][$s] !== null and $this->ploca[$redak][$s]->broj === $trenutni->broj) 
            {
                $trenutni->boja = 'crvena';
                return;
            }
        }

        // provjera postoji li uneseni broj u stupcu
        for($r = 0;$r < 6; $r++ )
        {
            if($r === $redak)
                continue;

            if($this->ploca[$r][$stupac] !== null and $this->ploca[$r][$stupac]->broj === $trenutni->broj) 
            {
                $trenutni->boja = 'crvena';
                return;
            }
        }

        if($redak===0 or $redak===1)
        {
            if($stupac>=0 and $stupac<=2 and $this->pretrazivanjeManjegPravokutnika($trenutni->broj,$redak,$stupac,0,0))
            {
                $trenutni->boja = 'crvena';
                return;
            }
            
            if($stupac>=3 and $stupac<=5 and $this->pretrazivanjeManjegPravokutnika($trenutni->broj,$redak,$stupac,0,3))
            {
                $trenutni->boja = 'crvena';
                return;
            }
        }
        if($redak===2 or $redak===3)
        {
            if($stupac>=0 and $stupac<=2 and $this->pretrazivanjeManjegPravokutnika($trenutni->broj,$redak,$stupac,2,0))
            {
                $trenutni->boja = 'crvena';
                return;
            }

            if($stupac>=3 and $stupac<=5 and $this->pretrazivanjeManjegPravokutnika($trenutni->broj,$redak,$stupac,2,3))
            {
                $trenutni->boja = 'crvena';
                return;
            }
        }
        if($redak===4 or $redak===5)
        {
            if($stupac>=0 and $stupac<=2 and $this->pretrazivanjeManjegPravokutnika($trenutni->broj,$redak,$stupac,4,0))
            {
                $trenutni->boja = 'crvena';
                return;
            }

            if($stupac>=3 and $stupac<=5 and $this->pretrazivanjeManjegPravokutnika($trenutni->broj,$redak,$stupac,4,3))
            {
                $trenutni->boja = 'crvena';
                return;
            }
        }

        //nema jednakog broja unesenom ni u retku ni u stupcu ni u manjem pravokutniku 
        $trenutni->boja = 'plava';
        return;
    }

    function updateColors()
    {
        for($s = 0; $s < 6; $s++)
        {
            for($r = 0; $r < 6; $r++)
            {
                if($this->ploca[$r][$s]===null or ($this->ploca[$r][$s]!==null and $this->ploca[$r][$s]->boja === 'crna'))
                    continue;

                else
                    $this->setColor($this->ploca[$r][$s], $r, $s);
            }
        }
    }

    // Vraca false ako korisnik zeli mijenjati pocetne brojeve na ploci ili one na kojima vec nesto pise
    // Inace, dodaje novi element na plocu sa prikladnom bojom
    function inputNumber($uneseniBroj, $redak, $stupac)
    {
        if($this->ploca[$redak][$stupac] === null or ($this->ploca[$redak][$stupac]!==null and $this->ploca[$redak][$stupac]->boja === 'crvena' or $this->ploca[$redak][$stupac]->boja === 'plava'))
        {
            $this->ploca[$redak][$stupac] = new Kvadratic($uneseniBroj);
        }

        if($this->ploca[$redak][$stupac]->boja === 'crna')
        {
            $this->errorMsg = 'Ne smije se mijenjati crno upisane brojeve.';
            return false;
        }

        $this->updateColors();
        return true;
    }

    // vraca true ako je doslo do pobjede, inace false
    function isVictory()
    {
        foreach ($this->ploca as $row) 
        {
            foreach ($row as $element)
            {
                if($element === null)
                    return false;

                if($element->boja === 'crvena')
                    return false;
            }
        }
        return true;
    }

    function obradiPokusaj()
	{
		// Vraća false ako nije odabrana opcija ili ako nije unesen broj od 1 do 6.
		// Inače, vraća 0 ako je došlo do pobjede ili 1 ako igra ide dalje.

		// Da li je igrač uopće odabrao opciju
		if( isset( $_POST['action_option'] ) )
		{
            if($_POST['action_option'] === "input_number")
            {
                if(isset($_POST['number']))
                {
                    $uneseniBroj = (int)$_POST['number'];
                    if($uneseniBroj < 1 || $uneseniBroj > 6)
                    {
                        $this->errorMsg = 'Uneseni broj mora biti cijeli broj od 1 do 6.';
                        return false; 
                    }

                    $provjera = $this->inputNumber($uneseniBroj,((int)$_POST['redak'])-1,((int)$_POST['stupac'])-1);
                    if(!$provjera)
                        return false;

                    // Ispravan je pokušaj
			        if($this->isVictory())
                        return RijesiSudoku::POBJEDA;
                    else
                        return RijesiSudoku::IGRA_IDE_DALJE;
                }
				return false;
            }
            if($_POST['action_option'] === "delete_number")
            {
                $redak = (int)$_POST['izbrisi_redak'];
                $stupac = (int)$_POST['izbrisi_stupac'];

                if($this->ploca[$redak-1][$stupac-1]!== null and $this->ploca[$redak-1][$stupac-1]->boja === 'crna')
                {
                    $this->errorMsg = 'Ne smije se brisati početne (crno obojene) brojeve. ';
                    return false;
                }
                
                $this->ploca[$redak-1][$stupac-1] = null;
                $this->updateColors();
                return RijesiSudoku::IGRA_IDE_DALJE;
            }
            if($_POST['action_option'] === "start_over")
            {
                return RijesiSudoku::KRENI_ISPOCETKA;
            }
		}

        if($this->brojPokusaja>0 and !isset( $_POST['action_option'] ) )
        {
            $this->errorMsg = 'Potrebno je odabrati opciju.';
            return false; 
        }
		// Igrač nije odabrao nijednu opciju
		return false;
	}


    function isGameOver() { return $this->gameOver; }


    function run()
    {
        // Funkcija obavlja "jedan potez" u igri.
		// Prvo, resetiraj poruke o greški.
		$this->errorMsg = false;

		// Prvo provjeri jel imamo uopće ime igraca
		if( $this->get_imeIgraca() === false )
		{
			// Ako nemamo ime igrača, ispiši formu za unos imena i to je kraj.
			$this->ispisiFormuZaIme();
			return;
		}

		// Dakle imamo ime igrača.
		// Ako je igrač pokušao pogoditi broj, provjerimo što se dogodilo s tim pokušajem.
		$rez = $this->obradiPokusaj();

		if( $rez === RijesiSudoku::POBJEDA )
		{
			// Ako je igrač pogodio, ispiši mu čestitku.
			$this->ispisiCestitku();
			$this->gameOver = true;
		}
		elseif($rez === RijesiSudoku::KRENI_ISPOCETKA)
        {
            $this->gameOver = true;
            $this->ispisiFormuZaIme();
			return;
        }
        else
            $this->ispisiFormuZaUpisivanjeBroja( $rez );
	}
};


// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------------
// Sad ide "glavni program" -- skroz generički, isti za svaku moguću igru.

// U $_SESSION ćemo čuvati cijeli objekt tipa RijesiSudoku
// U tom slučaju definicija klase treba biti PRIJE session_start();
session_start();

if( !isset( $_SESSION['igra'] ) )
{
    // Ako igra još nije započela, stvori novi objekt tipa RijesiSudoku i spremi ga u $_SESSION
    $igra = new RijesiSudoku();
    $_SESSION['igra'] = $igra;
}
else
{
    // Ako je igra već ranije započela, dohvati ju iz $_SESSION-a
    $igra = $_SESSION['igra'];
}

// Izvedi jedan korak u igri, u kojoj god fazi ona bila.
$igra->run();

if( $igra->isGameOver() )
{
    // Kraj igre -> prekini session.
    session_unset();
    session_destroy();
}
else
{
    // Igra još nije gotova -> spremi trenutno stanje u SESSION
    $_SESSION['igra'] = $igra;
}
