export class ExternalJs {
    constructor() {
        this.session = {}
        this.sessionPointer = 0
        this.scriptPointer = 0
    }

    add(...scriptSources) {
        ++this.sessionPointer
        this.session[this.sessionPointer] = []

        scriptSources.forEach(scriptSrc => {
            if (typeof scriptSrc === 'string') {
                this.addScript(scriptSrc)
            } else if (typeof scriptSrc === 'object') {
                Object.keys(scriptSrc).forEach(parentScriptSrc => {
                    this.addScript(parentScriptSrc).onload = () => {
                        scriptSrc[parentScriptSrc].forEach(childScriptSrc => {
                            this.addScript(childScriptSrc)
                        })
                    }
                })
            }
        })

        return this.sessionPointer
    }

    addScript(scriptSrc) {
        this.session[this.sessionPointer].push(++this.scriptPointer)

        let scriptElement = document.createElement('script')
        scriptElement.setAttribute('id', 'script' + this.scriptPointer)
        scriptElement.setAttribute('src', scriptSrc)
        document.body.appendChild(scriptElement)
        return scriptElement
    }

    remove(sessionPointer) {
        if (!this.session.hasOwnProperty(sessionPointer)) return
        this.session[sessionPointer].forEach(scriptPointer => {
            document.getElementById('script' + scriptPointer).remove()
        })
        delete this.session[sessionPointer]
    }
}

export const externalJs = new ExternalJs()
