
const baseDomain = "https://thecocktaildb.com/api/json/v1/1/";


//search cocktail by name
const searchDomain_Name = "search.php?s=";

//search all drinks by letter
const searchDomain_Letter = "search.php?f=";

//search ingredient by name
const searchDomain_Ingre = "search.php?i=";

//search cocktail by id;
const lookupDomain_Cocktail = "lookup.php?i="

//search ingredient by id
const lookupDomain_Ingre = "lookup.php?iid=" ;

//fetch to CocktailDB API
searchButton.addEventListener('click', () => {
    const apiURL = baseDomain + searchDomain_Name + drinkNameInput.value

    class Drink {
        constructor(id, name, alcoholic, ingredients, measurements, instructions, category, image, glass, tags) {
            this.id = id;
            this.name = name;
            this.alcoholic = alcoholic;
            this.ingredients = ingredients;
            this.instructions = instructions;
            this.measurements = measurements;
            this.category = category;
            this.image = image;
            this.glass = glass;
            this.tags = tags;
        }
    }

    fetch(apiURL)
        .then(response => response.json())
        .then(data=> {
            console.log(data);

            if (data.drinks && data.drinks.length > 0) {
                drinkResults.innerHTML = ""; // Clear previous results

                data.drinks.forEach(drinkData => {
                    const drinkName = drinkData.strDrink;
                    const drinkID = drinkData.idDrink;
                    const isAlcoholic = drinkData.strAlcoholic;

                    const ingredients = [];
                    const measurements = [];

                    //for loop to go through ingredients and measurements and append them into respective lists
                    for (let i = 1; i <= 15; i++) { // Assuming there are 15 possible ingredients
                        const ingredientKey = `strIngredient${i}`;
                        const measurementKey = `strMeasure${i}`;
                
                        const ingredient = drinkData[ingredientKey];
                        const measurement = drinkData[measurementKey];
                        
                        if (ingredient) {
                            ingredients.push(ingredient);

                        }
                        

                        if (measurement) {
                            measurements.push(measurement);
                        }
                        
                
                        // If both ingredient and measurement are null, break the loop
                        if (!ingredient && !measurements) {
                            break;
                        }
                    }

                    const category = drinkData.strCategory;
                    const img = drinkData.strDrinkThumb;
                    const glass = drinkData.strGlass;
                    const instructions = drinkData.strInstructions;
                
                    const drinkInfo = `
                        <li>Drink Name: ${drinkName}</li>
                        <li>Drink ID: ${drinkID}</li>
                        <li>Category: ${category}</li>
                        <li>Is Alcoholic?: ${isAlcoholic}</li>
                        <li>Ingredients: ${ingredients.join(', ')}</li>
                        <li>Measurements: ${measurements.join(', ')}</li>
                        <li>Instructions: ${instructions}</li>
                        <li>Glass: ${glass}</li>
                        <li>:Image: ${img}</li>
                        <hr>
                    `;

                    drinkResults.insertAdjacentHTML('beforeend', drinkInfo);
                });
            } else {
                drinkResults.innerHTML = "No drinks found for the given query.";
            }

        })
        .catch(error => {
            console.error("An error occured:",error);
        });
  
    console.log(apiURL)


});
