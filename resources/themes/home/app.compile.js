const dotenv = require('dotenv')
const dotenvExpand = require('dotenv-expand')
dotenvExpand(dotenv.config())

const sass = require('node-sass')
const fs = require('fs')
const path = require('path')
const outFile = 'public/css/style.min.css'
const outDir = path.dirname(outFile)
if (!fs.existsSync(outDir)) {
    fs.mkdirSync(outDir, {
        recursive: true,
    })
}
sass.render({
    file: 'src/assets/css/__style.scss',
    importer: function (url, prev, done) {
        const urls = url.split('/')
        url = urls.pop()
        if (!url.endsWith('.scss')) {
            url += '.scss'
        }
        const dir = path.dirname(prev) + (urls.length ? '/' + urls.join('/') : '') + '/'
        const tryFile1 = dir + '/' + url
        const tryFile2 = dir + '/_' + url
        const file = fs.existsSync(tryFile1) ? tryFile1 : (fs.existsSync(tryFile2) ? tryFile2 : false)
        if (file) {
            const contents = fs.readFileSync(file)
            return {
                contents: '$staticUrl: "' + process.env.VUE_APP_STATIC_URL + '";\n' + contents,
            }
        }
        return false
    },
    includePaths: ['src/assets/css/'],
    outFile: outFile,
    outputStyle: 'compressed',
}, function (error, result) {
    if (error) {
        console.log(error)
        return
    }

    fs.writeFile(outFile, result.css, function (err) {
        if (err) {
            console.log(err)
            return
        }
    })
})
