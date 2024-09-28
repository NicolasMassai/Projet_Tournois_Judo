import React, { useState, useEffect } from 'react';
import { constantes } from '../../constante';


export default function club() {


    const [clubs, setClub] = useState([]);



    useEffect(() => {
    fetch(constantes.url + '/club/JSON', {method : 'GET'})
    .then (response => response.json () )
    .then ( apiClub => {
        setClub(apiClub);

    })
    }, []);

    
    function update(id) {
        window.location.href = `/club/update/${id}`;    
    }

    function create() {
        window.location.href = `/club/create`;    
    }

    function Delete(id) {
        window.location.href = `/club/delete/${id}`;    
    }

    return (
        <main className='admin'>
            <h1 className='TitreAdmin'>Gestion des Club</h1>
            <div className='overflow'>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Ville</th>
                        <th>Pays</th>
                        <th>Email</th>
                    </tr>
                </thead>
                <tbody>
                    
                    {clubs.map(club => (
                            <tr key = {club.id}>
                                <td>{club.nom}</td>
                                <td>{club.ville}€</td>                                
                                <td>{club.pays}</td>
                                <td>{club.email}</td>
                                <td>
                                    {<button className = 'boutonProduit' type="button" onClick={(e) => update(club.id,e)}>
                                        Modifier
                                    </button>}

                                    {<button className = 'boutonProduit' type="button" onClick={(create)}>
                                        Créer
                                    </button>}
                                    
                                    {<button className = 'boutonProduit' type="button" onClick={(e) => Delete(club.id,e)}>
                                        Supprimer
                                    </button>}
                                </td>                            
                            </tr>    

                    ))}
               

                </tbody>
            </table>
            </div>

        </main>
        
    );


}