# TransATH-singularity
A command-line version of Faizah Aplop's TransATH tool.

I'm grabbing it with
````bash
git clone https://github.com/ramou/TransATH-singularity.git
````

Then I build it with 
````bash
cd TransATH-singularity/recipe
singularity build --sandbox /root/TransATH-sing/ Singularity
````
followed by
````bash
singularity build /root/TransATH.simg /root/TransATH-sing/
````
