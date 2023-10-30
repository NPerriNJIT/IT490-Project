import requests
import json
from sqlalchemy import create_engine, Column, Integer, String, Boolean, Text, Float, TIMESTAMP
from sqlalchemy.ext.declarative import declarative_base
from sqlalchemy.orm import sessionmaker

# Define the database connection
engine = create_engine('mysql://jheans:12345@localhost/testdb')
Base = declarative_base()

# Define the Drinks table structure
class Drinks(Base):
    __tablename__ = 'Drinks'
    drink_id = Column(Integer, primary_key=True, unique=True)
    drink_name = Column(String(60), nullable=False)
    drink_tags = Column(String(255))
    alcoholic = Column(Boolean, nullable=False)
    ingredients = Column(Text, nullable=False)
    measurements = Column(Text, nullable=False)
    instructions = Column(Text, nullable=False)
    avgrating = Column(Float)
    created = Column(TIMESTAMP, nullable=False)
    modified = Column(TIMESTAMP, server_default='CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP')

# Create the table if it doesn't exist
Base.metadata.create_all(engine)

# Define a function to call the API and populate the database
def populate_database(drink_id):
    api_url = f'https://www.thecocktaildb.com/api/json/v1/1/lookup.php?i={drink_id}'
    response = requests.get(api_url)
    data = response.json()

    if data['drinks'] is not None:
        drink = data['drinks'][0]

        # Create a new Drinks object
        new_drink = Drinks(
            drink_id=int(drink['idDrink']),
            drink_name=drink['strDrink'],
            drink_tags=drink['strTags'],
            alcoholic=drink['strAlcoholic'] == 'Alcoholic',
            ingredients=json.dumps([drink[f'strIngredient{i}'] for i in range(1, 16) if drink[f'strIngredient{i}']]),
            measurements=json.dumps([drink[f'strMeasure{i}'] for i in range(1, 16) if drink[f'strMeasure{i}']]),
            instructions=drink['strInstructions'],
            avgrating=None,  # You can set this field if you have a rating source
        )

        # Add the new drink to the database
        Session = sessionmaker(bind=engine)
        session = Session()
        session.add(new_drink)
        session.commit()
        session.close()

# Start populating the database from a specified drink_id
drink_id = 11000
while True:
    populate_database(drink_id)
    drink_id += 1
