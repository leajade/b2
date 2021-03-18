using System;
using System.Collections.Generic;
using System.Linq;
using System.Net.Http;
using System.Threading.Tasks;
using dotnet_projet.Models;
using Microsoft.AspNetCore.Mvc;
using Newtonsoft.Json;

// For more information on enabling MVC for empty projects, visit https://go.microsoft.com/fwlink/?LinkID=397860

namespace dotnet_projet.Controllers
{
    public class StationController : Controller
    {
        // GET: /<controller>/
        public IActionResult ListeDesStations()
        {
            var stations = GetStationsFromApi();
            return View(stations);
        }
        public IActionResult Carte()
        {
           
           
            return View();
        }
        private static List<Stations> GetStationsFromApi()
        {
            // Création d'un HttpClient (=outil qui va permettre d'interroger une URL via une requête HTTP)
            using (var client = new HttpClient())
            {
                //Interrogation de l'URL censée me retourner les données
                var response = client.GetAsync("https://api.alexandredubois.com/vcub-backend/vcub.php");
                //Récupération du corps de la réponse HTTP sous forme de chaîne de caractères
                var stringResult = response.Result.Content.ReadAsStringAsync();
                //Conversion de mon flux JSON (string) en une collection d'objects Stations
                //d'un flux de données vers des objets => Deserialisation
                //d'objets  vers une flux de données => serialisation
                var result = JsonConvert.DeserializeObject<List<Stations>>(stringResult.Result);
                return result;
            }
        }

    }
}
