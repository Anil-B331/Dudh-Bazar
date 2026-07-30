import { stitch } from "@google/stitch-sdk";

async function main() {
    console.log("Fetching project...");
    const project = stitch.project("6469487011324570728");
    const screens = await project.screens();
    
    const screenIds = [
        "f3d4d3c578454fe39a4784fb6fcb1a38", 
        "0b02313700394b17afb9e962bc738ae0", 
        "2b7b3d201e134cd38c4fcf6c00a199f1", 
        "48abc867f50e4307a5963292bd678b89", 
        "85ed95d0132941ecaa2bb0cbe95ba3bf", 
        "1147256aa1844c1e8b172bb10ba796c8", 
        "3fd3ecffea874d118bc5bb4412922676"  
    ];

    for (const screen of screens) {
        if (screenIds.includes(screen.id)) {
            const htmlUrl = await screen.getHtml();
            const imageUrl = await screen.getImage();
            console.log(`\nScreen ID: ${screen.id}`);
            console.log(`HTML: ${htmlUrl}`);
            console.log(`Image: ${imageUrl}`);
        }
    }
}

main().catch(console.error);
