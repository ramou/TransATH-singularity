# TransATH-singularity
A command-line version of Faizah Aplop's TransATH tool.


## Building the Image
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

## Using the image
````bash
singularity -W $PWD -H $PWD exec /root/TransATH.simg gblast.pl test.fasta out 1e-20 30
````
would execute the tool on sequences in `./test.fasta`, output results in a directory `./out` and make use of an `e-value` of `1e-20` and `coverage` of `30`.
