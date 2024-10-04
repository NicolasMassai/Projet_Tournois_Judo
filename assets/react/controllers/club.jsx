import React, { useState, useEffect } from 'react';
import { constantes } from '../../constante';


export default function club() {


    const [clubs, setClub] = useState([]);



    useEffect(() => {
    fetch(constantes.url + '/clubs/JSON', {method : 'GET'})
    .then (response => response.json () )
    .then ( apiClub => {
        setClub(apiClub);

    })
    }, []);



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
                    <div key={club.id} >
                        <p> <b>{club.nom}</b></p>
                    </div>

                ))}
                   
               

                </tbody>
            </table>
            </div>

        </main>
        
    );


}